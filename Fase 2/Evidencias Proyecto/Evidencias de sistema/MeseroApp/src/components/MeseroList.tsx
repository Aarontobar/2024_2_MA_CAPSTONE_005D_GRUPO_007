// src/components/MeseroList.tsx
import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL

interface Mesero {
  id_usuario: number;
  nombre: string;
}

const MeseroList = ({ navigation }: { navigation: any }) => {
  const [meseros, setMeseros] = useState<Mesero[]>([]);

  const fetchMeseros = () => {
    const url = getApiUrl('mesero/get_mesero.php');
    axios.get(url)
      .then(response => setMeseros(response.data))
      .catch(error => console.error(error));
  };

  useEffect(() => {
    fetchMeseros(); // Ejecutar la consulta inicialmente

    const interval = setInterval(() => {
      fetchMeseros(); // Ejecutar la consulta periódicamente
    }, 5000); // Intervalo de 5 segundos

    return () => clearInterval(interval); // Limpiar el intervalo al desmontar el componente
  }, []);

  return (
    <View style={styles.container}>
      <FlatList
        data={meseros}
        keyExtractor={item => item.id_usuario.toString()}
        renderItem={({ item }) => (
          <TouchableOpacity style={styles.item} onPress={() => navigation.navigate('MesasAsignadas', { id: item.id_usuario })}>
            <Text style={styles.text}>{item.nombre}</Text>
          </TouchableOpacity>
        )}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#fbf8f3',
  },
  item: {
    padding: 20,
    backgroundColor: '#f3f1ed',
    marginBottom: 20,
    borderRadius: 5,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  text: {
    fontSize: 18,
    color: '#333',
  },
});

export default MeseroList;