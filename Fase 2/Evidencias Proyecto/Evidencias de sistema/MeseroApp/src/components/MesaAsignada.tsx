import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, Alert, Button, TextInput, StatusBar, ScrollView } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api';
import * as NavigationBar from 'expo-navigation-bar';
import Icon from 'react-native-vector-icons/FontAwesome';

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
  const [searchText, setSearchText] = useState('');
  const [filter, setFilter] = useState('Todo');
  const [expandedMesaId, setExpandedMesaId] = useState<number | null>(null);

  useEffect(() => {
    NavigationBar.setVisibilityAsync('hidden');
    StatusBar.setHidden(true);

    fetchMesas();

    const interval = setInterval(() => {
      fetchMesas();
    }, 5000);

    return () => {
      clearInterval(interval);
      NavigationBar.setVisibilityAsync('visible');
      StatusBar.setHidden(false);
    };
  }, [id]);

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

  const toggleExpandState = (id_mesa: number) => {
    setExpandedMesaId(prevId => (prevId === id_mesa ? null : id_mesa));
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
        const responseData = response.data as { success: boolean, message: string };
        if (responseData.success) {
          Alert.alert('Éxito', 'El pedido ha sido marcado como entregado.');
          fetchMesas();
        } else {
          Alert.alert('Error', responseData.message);
        }
      })
      .catch(error => {
        console.error('Error al marcar el pedido como llevado:', error);
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
          fetchMesas();
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
    if (estado === 'Disponible') return '#8FBC8F'; // Mate green
    if (estado === 'Ocupada' && !pedido_activo) return '#D2B48C'; // Mate tan
    if (estado === 'Reservada') return '#4682B4'; // Mate steel blue
    if (estado === 'En Espera') return '#FFD700'; // Mate gold
    if (estado === 'Para Limpiar') return '#CD5C5C'; // Mate indian red
    if (pedido_activo) {
      if (pedido_activo.estado === 'preparado') return '#9370DB'; // Mate medium purple
      if (pedido_activo.estado === 'en preparación') return '#A52A2A'; // Mate brown
      if (pedido_activo.estado === 'recibido') return '#20B2AA'; // Mate light sea green
      if (pedido_activo.estado === 'servido') return '#FFB6C1'; // Mate light pink
      if (pedido_activo.estado === 'cancelado') return '#696969'; // Mate dim gray
    }
    return '#A9A9A9'; // Mate dark gray
  };

  const getIconName = (estado: string, estadoPedido?: string) => {
    if (estadoPedido) {
      switch (estadoPedido) {
        case 'preparado':
          return 'check-circle';
        case 'en preparación':
          return 'spinner';
        case 'recibido':
          return 'inbox';
        case 'servido':
          return 'utensils';
        case 'cancelado':
          return 'times-circle';
        default:
          return 'question-circle';
      }
    }
    switch (estado) {
      case 'Disponible':
        return 'check-circle';
      case 'Ocupada':
        return 'user';
      case 'Reservada':
        return 'bookmark';
      case 'En Espera':
        return 'clock';
      case 'Para Limpiar':
        return 'broom';
      default:
        return 'question-circle';
    }
  };

  const filteredMesas = mesas.filter(mesa => {
    const matchesSearch = mesa.id_mesa.toString().includes(searchText);
    const matchesFilter = filter === 'Todo' || mesa.estado === filter;
    return matchesSearch && matchesFilter;
  });

  return (
    <View style={styles.container}>
      <TouchableOpacity onPress={() => navigation.goBack()}>
        <Icon name="arrow-left" style={styles.backIcon} />
      </TouchableOpacity>
      <Text style={styles.title}>Mesas Asignadas</Text>
      <View style={styles.searchBar}>
        <TextInput
          style={styles.searchInput}
          placeholder="Buscar por ID de mesa"
          value={searchText}
          onChangeText={setSearchText}
        />
        <Text style={styles.searchIcon}>🔍</Text>
      </View>
      <ScrollView horizontal style={styles.filterContainer}>
        {['Todo', 'Disponible', 'Ocupada', 'Reservada', 'En Espera', 'Para Limpiar'].map(estado => (
          <TouchableOpacity
            key={estado}
            style={[styles.filterButton, filter === estado && styles.activeFilterButton]}
            onPress={() => setFilter(estado)}
          >
            <Text style={[styles.filterButtonText, filter === estado && styles.activeFilterButtonText]}>
              {estado}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>
      <FlatList
        data={filteredMesas}
        keyExtractor={item => item.id_mesa.toString()}
        renderItem={({ item }) => (
          <TouchableOpacity
            style={[styles.card, { backgroundColor: getBorderColor(item.estado, pedidos[item.id_mesa]), minHeight: expandedMesaId === item.id_mesa ? 200 : 150 }]}
            onPress={() => toggleExpandState(item.id_mesa)}
          >
            <Text style={styles.cardTitle}>Mesa {item.id_mesa}</Text>
            <Icon name={getIconName(item.estado, pedidos[item.id_mesa]?.estado)} style={styles.icon} />
            {expandedMesaId !== item.id_mesa && (
              <>
                <Text style={styles.cardText}>Estado: {item.estado}</Text>
                {pedidos[item.id_mesa] && (
                  <Text style={styles.centeredText}>Pedido: {pedidos[item.id_mesa].estado}</Text>
                )}
              </>
            )}
            {expandedMesaId === item.id_mesa && (
              <View style={styles.details}>
                {pedidos[item.id_mesa] && (
                  <>
                    <Text style={styles.detailsText}>ID del Pedido: {pedidos[item.id_mesa].id_pedido}</Text>
                    <Text style={styles.detailsText}>Total: ${pedidos[item.id_mesa].total_cuenta}</Text>
                    <Text style={styles.detailsText}>Hora del Pedido: {pedidos[item.id_mesa].hora}</Text>
                  </>
                )}
                <View style={styles.actions}>
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
                  {pedidos[item.id_mesa] && pedidos[item.id_mesa].estado === 'servido' && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => navigation.navigate('PagoScreen', { pedidoId: pedidos[item.id_mesa].id_pedido, meseroId: id })}
                    >
                      <Text style={styles.actionButtonText}>Pago</Text>
                    </TouchableOpacity>
                  )}
                  {pedidos[item.id_mesa] && ['recibido', 'en preparación', 'preparado'].includes(pedidos[item.id_mesa].estado) && (
                    <TouchableOpacity
                      style={styles.actionButton}
                      onPress={() => navigation.navigate('VerPedido', { id_pedido: pedidos[item.id_mesa].id_pedido })}
                    >
                      <Text style={styles.actionButtonText}>Ver Pedido</Text>
                    </TouchableOpacity>
                  )}
                </View>
              </View>
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
    paddingBottom: 80,
    backgroundColor: '#fbf8f3',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
    textAlign: 'center',
  },
  searchBar: {
    position: 'relative',
    marginBottom: 20,
  },
  searchInput: {
    width: '100%',
    padding: 10,
    paddingRight: 40,
    borderColor: '#ccc',
    borderWidth: 1,
    borderRadius: 5,
    fontSize: 16,
  },
  searchIcon: {
    position: 'absolute',
    right: 10,
    top: '50%',
    transform: [{ translateY: '-50%' }],
    fontSize: 18,
    color: '#2f4f4f',
  },
  filterContainer: {
    flexDirection: 'row',
    minHeight: 40,
    marginBottom: 20,
  },
  filterButton: {
    padding: 10,
    marginHorizontal: 5,
    borderColor: '#2f4f4f',
    borderWidth: 1,
    borderRadius: 5,
    backgroundColor: '#fffaf0',
    color: '#2f4f4f',
    cursor: 'pointer',
    fontSize: 14,
    maxHeight: 40,
  },
  activeFilterButton: {
    backgroundColor: '#2f4f4f',
  },
  filterButtonText: {
    color: '#2f4f4f',
  },
  activeFilterButtonText: {
    color: '#fff',
  },
  card: {
    padding: 20,
    borderRadius: 10,
    marginBottom: 20,
    textAlign: 'left',
    position: 'relative',
    minHeight: 150,
    cursor: 'pointer',
    justifyContent: 'space-between', // Ensure content is spaced out
  },
  cardTitle: {
    fontFamily: 'Playfair Display',
    fontSize: 24,
    margin: 0,
    position: 'absolute',
    top: 10,
    left: 10,
    color: '#fff',
  },
  cardText: {
    fontSize: 14,
    margin: 0,
    position: 'absolute',
    bottom: 10,
    left: 10,
    color: '#fff',
  },
  icon: {
    fontSize: 50,
    position: 'absolute',
    right: 10,
    top: '50%',
    transform: [{ translateY: '-50%' }],
    color: '#fff',
  },
  details: {
    fontSize: 14,
    marginTop: 10,
    flex: 1, // Ensure details take up remaining space
    justifyContent: 'center', // Center the details content
  },
  detailsText: {
    marginBottom: 10,
    color: '#fff',
    textAlign: 'center', // Center the text
  },
  actions: {
    marginTop: 'auto', // Ensure actions are at the bottom
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
    color: '#fff',
    textAlign: 'center',
  },
  centeredText: {
    fontSize: 16,
    textAlign: 'center',
    color: '#fff',
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: [{ translateX: -50 }, { translateY: -50 }],
  },
  backIcon: {
    fontSize: 24,
    color: '#000',
    marginBottom: 20,
  },
});

export default MesasAsignadas;