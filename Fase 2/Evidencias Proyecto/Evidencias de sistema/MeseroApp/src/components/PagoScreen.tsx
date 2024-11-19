import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, Button, Alert, TextInput, TouchableWithoutFeedback, Keyboard, TouchableOpacity, FlatList } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL

const PagoScreen = ({ route, navigation }: { route: any, navigation: any }) => {
  const { pedidoId, meseroId } = route.params;
  const [pedido, setPedido] = useState<any>(null);
  const [formaPago, setFormaPago] = useState<string>(''); // Estado para la forma de pago
  const [efectivo, setEfectivo] = useState<string>(''); // Estado para el monto en efectivo
  const [numPersonas, setNumPersonas] = useState<string>(''); // Estado para el número de personas

  const fetchPedido = () => {
    const url = getApiUrl(`mesero/get_pedido.php?id_pedido=${pedidoId}`);
    axios.get(url)
      .then(response => {
        if (response.data.success) {
          setPedido(response.data.pedido);
        } else {
          Alert.alert('Error', response.data.message);
        }
      })
      .catch(error => console.error(error));
  };

  useEffect(() => {
    fetchPedido();
  }, []);

  const actualizarEstadoPedido = () => {
    const url = getApiUrl(`mesero/update_pedido.php?id_pedido=${pedidoId}&estado_pago=pagado&estado=completado&estado_mesa=Para Limpiar`);
    
    axios.get(url)
      .then(response => {
        if (response.data.success) {
          Alert.alert('Éxito', 'El pedido ha sido actualizado.', [
            { text: 'OK', onPress: () => navigation.navigate('MesasAsignadas', { id: meseroId }) }
          ]);
        } else {
          console.error('Error en la respuesta del servidor:', response.data);
          Alert.alert('Error', response.data.message || 'Error desconocido');
        }
      })
      .catch(error => {
        console.error('Error en la solicitud:', error);
        Alert.alert('Error', error.message || 'Error desconocido');
      });
  };

  const calcularVuelto = () => {
    const vuelto = parseFloat(efectivo) - pedido.total_cuenta;
    return vuelto >= 0 ? vuelto.toFixed(2) : 'Monto insuficiente';
  };

  const calcularPagoPorPersona = () => {
    const total = pedido.total_cuenta;
    const personas = parseInt(numPersonas);
    if (personas > 0) {
      return (total / personas).toFixed(2);
    }
    return 'Número de personas inválido';
  };

  const renderPagoPorPersona = () => {
    const personas = parseInt(numPersonas);
    if (personas > 0) {
      return Array.from({ length: personas }, (_, index) => (
        <View key={index} style={styles.personaContainer}>
          <Text style={styles.text}>Persona {index + 1}</Text>
          <Text style={styles.text}>Pago: ${calcularPagoPorPersona()}</Text>
          <Button
            title="Confirmar Pago"
            onPress={() => Alert.alert('Pago', `El pago de la persona ${index + 1} ha sido realizado.`)}
          />
        </View>
      ));
    }
    return null;
  };

  if (!pedido) {
    return (
      <View style={styles.container}>
        <Text>Cargando...</Text>
      </View>
    );
  }

  return (
    <TouchableWithoutFeedback onPress={Keyboard.dismiss}>
      <FlatList
        data={[{ key: 'content' }]}
        renderItem={() => (
          <View style={styles.receipt}>
            <Text style={styles.header}>Restaurante XYZ</Text>
            <Text style={styles.subHeader}>Comprobante de Pago</Text>
            <View style={styles.divider} />
            <Text style={styles.text}>Pedido ID: {pedido.id_pedido}</Text>
            <Text style={styles.text}>Estado: {pedido.estado}</Text>
            <Text style={styles.text}>Total: ${pedido.total_cuenta}</Text>
            <FlatList
              data={pedido.platillos}
              keyExtractor={item => item.id_platillo.toString()}
              renderItem={({ item }) => (
                <View style={styles.platilloItem}>
                  <Text style={styles.platilloText}>{item.nombre_platillo} - ${item.precio}</Text>
                </View>
              )}
            />
            <View style={styles.divider} />
            <Text style={styles.text}>Forma de Pago:</Text>
            <View style={styles.radioContainer}>
              <TouchableOpacity style={styles.radioButton} onPress={() => setFormaPago('efectivo')}>
                <View style={[styles.radioCircle, formaPago === 'efectivo' && styles.selectedRadio]} />
                <Text style={styles.radioText}>Efectivo</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.radioButton} onPress={() => setFormaPago('tarjeta')}>
                <View style={[styles.radioCircle, formaPago === 'tarjeta' && styles.selectedRadio]} />
                <Text style={styles.radioText}>Tarjeta</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.radioButton} onPress={() => setFormaPago('dividir')}>
                <View style={[styles.radioCircle, formaPago === 'dividir' && styles.selectedRadio]} />
                <Text style={styles.radioText}>Dividir Pago</Text>
              </TouchableOpacity>
            </View>
            {formaPago === 'efectivo' && (
              <View style={styles.efectivoContainer}>
                <TextInput
                  style={styles.input}
                  placeholder="Ingrese el monto en efectivo"
                  keyboardType="numeric"
                  value={efectivo}
                  onChangeText={setEfectivo}
                />
                {efectivo && (
                  <Text style={styles.text}>Vuelto: ${calcularVuelto()}</Text>
                )}
                <Button
                  title="Confirmar Pago"
                  onPress={actualizarEstadoPedido}
                  style={styles.confirmButton}
                />
              </View>
            )}
            {formaPago === 'tarjeta' && (
              <Button
                title="Pagar con PayPal"
                onPress={() => {
                  Alert.alert('Pago', 'Redirigiendo a PayPal...');
                  actualizarEstadoPedido();
                }}
                style={styles.confirmButton}
              />
            )}
            {formaPago === 'dividir' && (
              <View style={styles.dividirContainer}>
                <Text style={styles.text}>Dividir por número de personas:</Text>
                <TextInput
                  style={styles.input}
                  placeholder="Número de personas"
                  keyboardType="numeric"
                  value={numPersonas}
                  onChangeText={setNumPersonas}
                />
                {numPersonas && renderPagoPorPersona()}
              </View>
            )}
          </View>
        )}
        keyExtractor={item => item.key}
        contentContainerStyle={styles.container}
      />
    </TouchableWithoutFeedback>
  );
};

const styles = StyleSheet.create({
  container: {
    flexGrow: 1,
    padding: 20,
    backgroundColor: '#fbf8f3',
    justifyContent: 'center',
    alignItems: 'center',
    paddingBottom: 100, // Añadir margen inferior para evitar que el botón sea tapado
  },
  receipt: {
    width: '100%',
    padding: 20,
    backgroundColor: '#fff',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#ddd',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 3,
  },
  header: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 10,
  },
  subHeader: {
    fontSize: 18,
    textAlign: 'center',
    marginBottom: 20,
  },
  divider: {
    borderBottomColor: '#ddd',
    borderBottomWidth: 1,
    marginVertical: 10,
  },
  text: {
    fontSize: 18,
    color: '#333',
    marginBottom: 10,
  },
  radioContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 20,
  },
  radioButton: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  radioCircle: {
    height: 20,
    width: 20,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#333',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  selectedRadio: {
    backgroundColor: '#333',
  },
  radioText: {
    fontSize: 18,
    color: '#333',
  },
  efectivoContainer: {
    marginTop: 20,
  },
  input: {
    height: 40,
    borderColor: '#ccc',
    borderWidth: 1,
    borderRadius: 5,
    paddingHorizontal: 10,
    marginBottom: 10,
  },
  dividirContainer: {
    marginTop: 20,
  },
  platilloItem: {
    padding: 10,
    borderBottomColor: '#ddd',
    borderBottomWidth: 1,
  },
  platilloText: {
    fontSize: 16,
  },
  selectedText: {
    color: 'green',
    fontSize: 16,
  },
  personaContainer: {
    marginBottom: 20,
  },
  confirmButton: {
    marginTop: 20,
  },
});

export default PagoScreen;