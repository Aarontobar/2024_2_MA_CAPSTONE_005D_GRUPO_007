// src/components/CartScreen.tsx
import React from 'react';
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
  quantity: number;
}

const CartScreen = ({ route, navigation }: { route: any, navigation: any }) => {
  const { carrito, mesaId, userId } = route.params;

  const getTotalPrice = () => {
    return carrito.reduce((total: number, item: Platillo) => total + (item.precio * item.quantity), 0).toFixed(2);
  };
  
  const getTotalItems = () => {
    return carrito.reduce((total: number, item: Platillo) => total + item.quantity, 0);
  };
  
  const confirmOrder = () => {
    const totalPrice = getTotalPrice();
    console.log(`Enviando pedido con ID de mesa: ${mesaId}, ID de usuario: ${userId}, y total: ${totalPrice}`);
    Alert.alert('Confirmación', `Enviando pedido con ID de mesa: ${mesaId}, ID de usuario: ${userId}, y total: ${totalPrice}`);
  
    const url = getApiUrl(`menu/generar_pedido_app.php?mesa_id=${mesaId}&id_mesero=${userId}&total=${totalPrice}`);
    const carritoParams = carrito.map(item => `carrito[]=${item.id_platillo},${item.quantity},${item.precio}`).join('&');

    axios.post(`${url}&${carritoParams}`)
      .then(response => {
        const data = response.data as { success: boolean; message?: string };
        if (data.success) {
          Alert.alert('Pedido confirmado', 'Tu pedido ha sido confirmado.');
          navigation.navigate('MesasAsignadas', { id: userId });
        } else {
          Alert.alert('Error', data.message || 'Hubo un problema al confirmar el pedido.');
        }
      })
      .catch(error => {
        console.error('Error al confirmar el pedido:', error);
        Alert.alert('Error', 'Hubo un problema al confirmar el pedido.');
      });
  };
  
  const totalItems = getTotalItems();
  const shouldShowConfirmButton = totalItems > 0;
  console.log(`Cantidad de ítems en el carrito: ${totalItems}`);
  console.log(`Mostrar botón de confirmar pedido: ${shouldShowConfirmButton}`);
  
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Carrito</Text>
      {carrito.length === 0 ? (
        <Text style={styles.emptyCartText}>No hay productos en el carrito.</Text>
      ) : (
        <FlatList
          data={carrito}
          keyExtractor={item => item.id_platillo.toString()}
          renderItem={({ item }) => (
            <View style={styles.cartItem}>
              <Image
                source={imageMap[item.ruta_foto]}
                style={styles.cartItemImage}
                onError={() => console.log(`Error loading image: ${item.ruta_foto}`)}
              />
              <View style={styles.cartItemDetails}>
                <Text style={styles.cartItemTitle}>{item.nombre}</Text>
                <Text style={styles.cartItemText}>Cantidad: {item.quantity}</Text>
                <Text style={styles.cartItemText}>Precio Unitario: ${Number(item.precio).toFixed(2)}</Text>
                <Text style={styles.cartItemText}>Total: ${(item.precio * item.quantity).toFixed(2)}</Text>
              </View>
            </View>
          )}
        />
      )}
      <View style={styles.totalSection}>
        <Text style={styles.totalText}>Total: ${getTotalPrice()}</Text>
        {shouldShowConfirmButton && (
          <TouchableOpacity style={[styles.confirmButton, { marginTop: 40 }]} onPress={confirmOrder}>
            <Text style={styles.confirmButtonText}>Confirmar Pedido</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    paddingBottom: 90, // Agrega un margen inferior
    backgroundColor: '#1a1a1a',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 20,
  },
  emptyCartText: {
    fontSize: 18,
    color: '#ccc',
  },
  cartItem: {
    flexDirection: 'row',
    marginBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#333',
    paddingBottom: 10,
  },
  cartItemImage: {
    width: 120,
    height: 120,
    borderRadius: 10,
  },
  cartItemDetails: {
    marginLeft: 20,
    justifyContent: 'center',
  },
  cartItemTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#fff',
  },
  cartItemText: {
    fontSize: 16,
    color: '#ccc',
  },
  totalSection: {
    marginTop: 20,
    alignItems: 'center',
  },
  totalText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
  },
  confirmButton: {
    backgroundColor: '#52443d',
    padding: 15,
    borderRadius: 5,
    marginTop: 20,
  },
  confirmButtonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 18,
  },
});

export default CartScreen;