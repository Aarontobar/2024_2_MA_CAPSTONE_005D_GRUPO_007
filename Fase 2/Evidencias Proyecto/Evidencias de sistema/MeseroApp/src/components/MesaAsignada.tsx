import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, Alert, Button } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL

interface PedidoActivo {
  id_pedido: number;
  estado: string;
  total_cuenta: number;
  hora: string;
}

interface Mesa {
  id_mesa: number;
  estado: string;
  cantidad_asientos: number;
  pedido_activo?: PedidoActivo;
  active?: boolean;
}

const MesasAsignadas = ({ route, navigation }: { route: any, navigation: any }) => {
  const { id } = route.params;
  const [mesas, setMesas] = useState<Mesa[]>([]);
  const [pedidos, setPedidos] = useState<{ [key: number]: PedidoActivo }>({});

  const fetchMesas = () => {
    const url = getApiUrl(`mesero/get_mesas_app.php?id_usuario=${id}`);
    axios.get(url)
      .then(response => {
        const data = response.data as { mesas: Mesa[], pedidos: { [key: number]: PedidoActivo } };
        setMesas(data.mesas);
        setPedidos(data.pedidos);
      })
      .catch(error => console.error(error));
  };

  useEffect(() => {
    fetchMesas(); // Ejecutar la consulta inicialmente

    const interval = setInterval(() => {
      fetchMesas(); // Ejecutar la consulta periódicamente
    }, 5000); // Intervalo de 5 segundos

    return () => clearInterval(interval); // Limpiar el intervalo al desmontar el componente
  }, [id]);

  const toggleActiveState = (id_mesa: number) => {
    setMesas(prevMesas =>
      prevMesas.map(mesa =>
        mesa.id_mesa === id_mesa ? { ...mesa, active: !mesa.active } : mesa
      )
    );
  };

  const marcarPedidoComoLlevado = (id_pedido: number) => {
    const url = getApiUrl('mesero/estado_pedido_app.php');
    const payload = new URLSearchParams();
    payload.append('id_pedido', id_pedido.toString());
  
    axios.post(url, payload.toString(), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    })
      .then(response => {
        console.log('Response data:', response.data); // Log the response data
        const responseData = response.data as { success: boolean, message: string };
        if (responseData.success) {
          Alert.alert('Éxito', 'El pedido ha sido marcado como entregado.');
          fetchMesas(); // Actualizar las mesas después de marcar el pedido como llevado
        } else {
          console.error('Error response:', responseData); // Log the error response
          Alert.alert('Error', responseData.message);
        }
      })
      .catch(error => {
        console.error('Error al marcar el pedido como llevado:', error); // Log the error
        Alert.alert('Error', 'Hubo un problema al marcar el pedido como llevado.');
      });
  };

  const marcarMesaComoLimpia = (id_mesa: number) => {
    const url = getApiUrl('mesero/marcar_mesa_limpia_app.php');
    const payload = new URLSearchParams();
    payload.append('id_mesa', id_mesa.toString());
  
    axios.post(url, payload.toString(), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    })
      .then(response => {
        const responseData = response.data as { success: boolean, message: string };
        if (responseData.success) {
          fetchMesas(); // Actualizar las mesas después de marcar la mesa como limpia
        } else {
          Alert.alert('Error', responseData.message);
        }
      })
      .catch(error => {
        console.error('Error al marcar la mesa como limpia:', error);
        Alert.alert('Error', 'Hubo un problema al marcar la mesa como limpia.');
      });
  };

  const getBorderColor = (estado: string, pedido_activo?: PedidoActivo) => {
    if (estado === 'Disponible') return 'green';
    if (estado === 'Ocupada' && !pedido_activo) return 'orange';
    if (estado === 'Reservada') return 'blue';
    if (estado === 'En Espera') return 'yellow';
    if (estado === 'Para Limpiar') return 'red';
    if (pedido_activo) {
      if (pedido_activo.estado === 'preparado') return 'purple';
      if (pedido_activo.estado === 'en preparación') return 'brown';
      if (pedido_activo.estado === 'recibido') return 'cyan';
      if (pedido_activo.estado === 'servido') return 'pink';
      if (pedido_activo.estado === 'completado') return 'gray';
      if (pedido_activo.estado === 'cancelado') return 'black';
    }
    return 'gray';
  };

  return (
    <View style={[styles.container, { paddingBottom: 40 }]}>
      <FlatList
        data={mesas}
        keyExtractor={item => item.id_mesa.toString()}
        renderItem={({ item }) => (
          <TouchableOpacity
            style={[styles.item, { borderColor: getBorderColor(item.estado, pedidos[item.id_mesa]) }]}
            onPress={() => toggleActiveState(item.id_mesa)}
          >
            <Text style={styles.text}>Mesa ID: {item.id_mesa}</Text>
            <Text style={styles.text}>Estado: {item.estado}</Text>
            <Text style={styles.text}>Asientos: {item.cantidad_asientos}</Text>
            {item.active && (
              <>
                {pedidos[item.id_mesa] && (
                  <>
                    <Text style={styles.text}>Pedido ID: {pedidos[item.id_mesa].id_pedido}</Text>
                    <Text style={styles.text}>Estado del Pedido: {pedidos[item.id_mesa].estado}</Text>
                    <Text style={styles.text}>Total: ${pedidos[item.id_mesa].total_cuenta}</Text>
                    <Text style={styles.text}>Hora del Pedido: {pedidos[item.id_mesa].hora}</Text>
                  </>
                )}
                <View style={styles.details}>
                  {item.estado === 'Ocupada' && !pedidos[item.id_mesa] && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => navigation.navigate('MenuScreen', { mesaId: item.id_mesa, userId: id })}
                    >
                      <Text style={styles.actionButtonText}>Tomar Pedido</Text>
                    </TouchableOpacity>
                  )}
                  {item.estado === 'Para Limpiar' && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => marcarMesaComoLimpia(item.id_mesa)}
                    >
                      <Text style={styles.actionButtonText}>Marcar como Limpia</Text>
                    </TouchableOpacity>
                  )}
                  {pedidos[item.id_mesa] && pedidos[item.id_mesa].estado === 'preparado' && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => marcarPedidoComoLlevado(pedidos[item.id_mesa].id_pedido)}
                    >
                      <Text style={styles.actionButtonText}>Marcar como Entregado</Text>
                    </TouchableOpacity>
                  )}
                  {pedidos[item.id_mesa] && pedidos[item.id_mesa].estado === 'completado' && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => navigation.navigate('PagoScreen', { pedidoId: pedidos[item.id_mesa].id_pedido })}
                    >
                      <Text style={styles.actionButtonText}>Pago</Text>
                    </TouchableOpacity>
                  )}
                </View>
              </>
            )}
          </TouchableOpacity>
        )}
      />
      <View style={{ marginBottom: 40 }}>
        <Button
          title="Abrir Chat"
          onPress={() => navigation.navigate('Chat', { id_usuario: id })}
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    paddingBottom: 80, // Agrega un margen inferior
    backgroundColor: '#fbf8f3',
  },
  item: {
    padding: 20,
    backgroundColor: '#fff',
    marginBottom: 20,
    borderRadius: 10,
    borderWidth: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 3,
  },
  text: {
    fontSize: 18,
    color: '#333',
  },
  actions: {
    marginTop: 10,
  },
  actionButton: {
    backgroundColor: '#5cb85c',
    padding: 10,
    borderRadius: 4,
    marginTop: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 3,
  },
  actionButtonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
  details: {
    marginTop: 10,
  },
});

export default MesasAsignadas;