// src/components/MenuScreen.tsx
import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, Image, Alert } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL
import imageMap from '../utils/imageMap'; // Importa el mapeo de imágenes

interface Platillo {
  id_platillo: number;
  nombre: string;
  descripcion: string;
  precio: number;
  ruta_foto: string;
  tipo_platillo: string;
  quantity?: number;
}

const MenuScreen = ({ route, navigation }: { route: any, navigation: any }) => {
  const { mesaId, userId } = route.params;
  const [platillos, setPlatillos] = useState<Platillo[]>([]);
  const [carrito, setCarrito] = useState<Platillo[]>([]);
  const [tipoPlatillo, setTipoPlatillo] = useState('Plato Principal');

  const fetchPlatillos = () => {
    const url = getApiUrl(`menu/get_platillos.php?tipo_platillo=${tipoPlatillo}`);
    axios.get(url)
      .then(response => {
        setPlatillos(response.data.platillos);
      })
      .catch(error => console.error(error));
  };

  useEffect(() => {
    fetchPlatillos();
  }, [tipoPlatillo]);

  const addToCart = (platillo: Platillo) => {
    setCarrito(prevCarrito => {
      const existingItem = prevCarrito.find(item => item.id_platillo === platillo.id_platillo);
      if (existingItem) {
        return prevCarrito.map(item =>
          item.id_platillo === platillo.id_platillo
            ? { ...item, quantity: (item.quantity || 1) + 1 }
            : item
        );
      } else {
        return [...prevCarrito, { ...platillo, quantity: 1 }];
      }
    });
    Alert.alert('Éxito', 'Platillo agregado al carrito.');
  };

  const removeFromCart = (id_platillo: number) => {
    setCarrito(prevCarrito => {
      const existingItem = prevCarrito.find(item => item.id_platillo === id_platillo);
      if (existingItem && (existingItem.quantity ?? 0) > 1) {
        return prevCarrito.map(item =>
          item.id_platillo === id_platillo
            ? { ...item, quantity: (item.quantity || 1) - 1 }
            : item
        );
      } else {
        return prevCarrito.filter(item => item.id_platillo !== id_platillo);
      }
    });
    Alert.alert('Éxito', 'Platillo eliminado del carrito.');
  };

  const getTotalItemsInCart = () => {
    return carrito.reduce((total, item) => total + (item.quantity || 1), 0);
  };

  return (
    <View style={styles.container}>
      <View style={styles.navbar}>
        <Text style={styles.logo}>Tomoshibi</Text>
        <TouchableOpacity
          style={styles.cartButton}
          onPress={() => navigation.navigate('CartScreen', { carrito, mesaId, userId })}
        >
          <Text style={styles.cartText}>Carrito ({getTotalItemsInCart()})</Text>
        </TouchableOpacity>
      </View>
      <FlatList
        ListHeaderComponent={
          <>
            <View style={styles.navItems}>
              <TouchableOpacity onPress={() => setTipoPlatillo('Entrada')}>
                <Text style={styles.navItem}>Entradas</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setTipoPlatillo('Plato Principal')}>
                <Text style={styles.navItem}>Platos Principales</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setTipoPlatillo('Acompañamientos')}>
                <Text style={styles.navItem}>Acompañamientos</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setTipoPlatillo('Postres')}>
                <Text style={styles.navItem}>Postres</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setTipoPlatillo('Bebida')}>
                <Text style={styles.navItem}>Bebidas</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setTipoPlatillo('Menú Infantil')}>
                <Text style={styles.navItem}>Menú Infantil</Text>
              </TouchableOpacity>
            </View>
            <View style={styles.hero}>
              <Image
                source={{ uri: `../../assets/images/banner_${tipoPlatillo.toLowerCase().replace(' ', '_')}.jpg` }}
                style={styles.heroImage}
              />
            </View>
            <View style={styles.menu}>
              <Text style={styles.menuTitle}>{tipoPlatillo}</Text>
            </View>
          </>
        }
        data={platillos}
        keyExtractor={item => item.id_platillo.toString()}
        renderItem={({ item }) => {
          const isInCart = carrito.some(cartItem => cartItem.id_platillo === item.id_platillo);
          return (
            <View style={styles.mealItem}>
              <View style={styles.mealDescription}>
                <Text style={styles.mealTitle}>{item.nombre}</Text>
                <Text style={styles.mealText}>{item.descripcion}</Text>
                <Text style={styles.mealText}>Precio: ${item.precio}</Text>
                <View style={styles.buttons}>
                  <TouchableOpacity
                    style={styles.button}
                    onPress={() => addToCart(item)}
                  >
                    <Text style={styles.buttonText}>Ordenar</Text>
                  </TouchableOpacity>
                  {isInCart && (
                    <TouchableOpacity
                      style={styles.button}
                      onPress={() => removeFromCart(item.id_platillo)}
                    >
                      <Text style={styles.buttonText}>Eliminar</Text>
                    </TouchableOpacity>
                  )}
                </View>
              </View>
              <Image
                source={imageMap[item.ruta_foto]}
                style={styles.mealImage}
                onError={() => console.log(`Error loading image: ${item.ruta_foto}`)}
              />
            </View>
          );
        }}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#1a1a1a',
  },
  navbar: {
    backgroundColor: '#262626',
    padding: 10,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    width: '100%',
    zIndex: 1000,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.5,
    shadowRadius: 5,
  },
  logo: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
  },
  navItems: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    flexWrap: 'wrap',
    padding: 10,
    backgroundColor: '#262626',
  },
  navItem: {
    color: '#fff',
    fontSize: 16,
    textDecorationLine: 'none',
    margin: 5,
  },
  cartButton: {
    backgroundColor: '#333',
    padding: 10,
    borderRadius: 5,
  },
  cartText: {
    color: '#fff',
    fontSize: 16,
  },
  hero: {
    marginTop: 10,
  },
  heroImage: {
    width: '100%',
    height: 200,
  },
  menu: {
    padding: 20,
  },
  menuTitle: {
    fontSize: 24,
    color: '#fff',
    marginBottom: 20,
  },
  mealItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#333',
    paddingBottom: 10,
  },
  mealDescription: {
    maxWidth: '60%',
  },
  mealTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#fff',
  },
  mealText: {
    fontSize: 16,
    color: '#ccc',
  },
  mealImage: {
    width: 120,
    height: 120,
    borderRadius: 10,
  },
  buttons: {
    flexDirection: 'row',
    marginTop: 10,
  },
  button: {
    backgroundColor: '#52443d',
    padding: 10,
    borderRadius: 5,
    marginRight: 10,
  },
  buttonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
});

export default MenuScreen;