import React, { useEffect, useState } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL
import gifMap from '../utils/gifMap'; // Importa el mapeo de GIFs

const VerPedido = ({ route, navigation }: { route: any, navigation: any }) => {
  const { id_pedido } = route.params;
  const [orderInfo, setOrderInfo] = useState<any>(null);
  const [estado, setEstado] = useState<string>('');
  const [tipo, setTipo] = useState<string>('');
  const [progressPercentage, setProgressPercentage] = useState<number>(0);
  const [imageUrl, setImageUrl] = useState<any>(null);

  useEffect(() => {
    fetchOrderInfo();
    const interval = setInterval(() => fetchOrderInfo(), 5000); // Actualiza cada 5 segundos
    return () => clearInterval(interval);
  }, []);

  const fetchOrderInfo = async () => {
    try {
      const response = await axios.get(getApiUrl(`menu/estado.php?id=${id_pedido}`));
      const data = response.data;
      if ((data as any).error) {
        console.error('Error:', (data as any).error);
        return;
      }
      const typedData = data as { estado: string, [key: string]: any };
      if (typedData.estado !== estado) {
        setEstado(typedData.estado);
        updateStatusImage(typedData.estado);
      }
      setOrderInfo(data);
    } catch (error) {
      console.error('Error fetching order info:', error);
    }
  };

  const updateStatusImage = (estado: string) => {
    let imageUrl = ''; // Imagen predeterminada
    let progressPercentage = 0; // Progreso predeterminado

    switch (estado) {
      case 'recibido':
        imageUrl = getRandomGifPath('recibido');
        progressPercentage = 25;
        break;
      case 'en preparación':
        imageUrl = getRandomGifPath('en_preparacion');
        progressPercentage = 50;
        break;
      case 'preparado':
        imageUrl = getRandomGifPath('preparado');
        progressPercentage = 75;
        break;
      case 'servido':
        imageUrl = getRandomGifPath('servido');
        progressPercentage = 100;
        break;
    }

    setImageUrl(imageUrl);
    setProgressPercentage(progressPercentage);
  };

  const getRandomGifPath = (folder: string) => {
    const gifCount = 5; // Supón que hay un número conocido de gifs en cada carpeta
    const randomIndex = Math.floor(Math.random() * gifCount) + 1;
    const gifKey = `${folder}${randomIndex}.gif`;
    console.log(`Buscando GIF: ${gifKey}`);
    return gifMap[gifKey];
  };

  const handleAction = () => {
    if (tipo === 'Para Llevar') {
      // Redirigir a la página de dejar reseña
      navigation.navigate('DejarReseña', { id_pedido });
    } else {
      // Redirigir a la página de pago
      navigation.navigate('Pagar', { id_pedido });
    }
  };

  const handleLogout = () => {
    navigation.navigate('Login');
  };

  const getStatusStyle = (currentStatus: string) => {
    return estado === currentStatus ? styles.activeStatusText : styles.statusText;
  };

  if (!orderInfo) {
    return (
      <View style={styles.container}>
        <Text style={styles.title}>Cargando...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Progreso de su Pedido</Text>
      {imageUrl && <Image source={imageUrl} style={styles.statusImage} />}
      <View style={styles.progressContainer}>
        <View style={styles.progressBarBackground}>
          <View style={[styles.progressBar, { width: `${progressPercentage}%` }]} />
        </View>
        <Text style={styles.progressText}>{progressPercentage}%</Text>
      </View>
      <TouchableOpacity style={styles.actionButton} onPress={handleAction}>
        <Text style={styles.actionButtonText}>{tipo === 'Para Llevar' ? 'Dejar Reseña' : 'Pagar'}</Text>
      </TouchableOpacity>
      {estado === 'servido' && (
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <Text style={styles.logoutButtonText}>Salir</Text>
        </TouchableOpacity>
      )}
      <View style={styles.orderStatus}>
        <View style={styles.statusItem}>
          <Text style={getStatusStyle('recibido')}>Pedido recibido</Text>
        </View>
        <View style={styles.statusItem}>
          <Text style={getStatusStyle('en preparación')}>Estamos preparando tu pedido</Text>
        </View>
        <View style={styles.statusItem}>
          <Text style={getStatusStyle('preparado')}>Tu pedido está preparado</Text>
        </View>
        <View style={styles.statusItem}>
          <Text style={getStatusStyle('servido')}>Ya recibiste tu pedido</Text>
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#1a1a1a',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 20,
  },
  statusImage: {
    width: 200,
    height: 200,
    borderRadius: 100, // Hace que la imagen sea circular
    marginBottom: 20,
    alignSelf: 'center', // Centra la imagen
  },
  actionButton: {
    display: 'none', // Ocultar por defecto
    marginTop: 20,
    padding: 10,
    backgroundColor: 'green',
    borderRadius: 5,
  },
  actionButtonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
  logoutButton: {
    marginTop: 20,
    padding: 10,
    backgroundColor: 'red',
    borderRadius: 5,
  },
  logoutButtonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
  progressContainer: {
    marginTop: 20,
    alignItems: 'center',
  },
  progressBarBackground: {
    width: '100%',
    height: 20,
    backgroundColor: '#333',
    borderRadius: 5,
    overflow: 'hidden',
  },
  progressBar: {
    height: '100%',
    backgroundColor: 'green',
  },
  progressText: {
    marginTop: 10,
    color: '#fff',
  },
  orderStatus: {
    marginTop: 20,
  },
  statusItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
  },
  statusText: {
    fontSize: 16,
    color: '#fff',
  },
  activeStatusText: {
    fontSize: 20,
    color: 'green',
    fontWeight: 'bold',
  },
});

export default VerPedido;