import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, Button, Alert, TextInput, TouchableWithoutFeedback, Keyboard } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import axios from 'axios';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL

const PagoScreen = ({ route, navigation }: { route: any, navigation: any }) => {
  const { pedidoId } = route.params;
  const [pedido, setPedido] = useState<any>(null);
  const [formaPago, setFormaPago] = useState<string>(''); // Estado para la forma de pago
  const [efectivo, setEfectivo] = useState<string>(''); // Estado para el monto en efectivo

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

  const calcularVuelto = () => {
    const vuelto = parseFloat(efectivo) - pedido.total_cuenta;
    return vuelto >= 0 ? vuelto.toFixed(2) : 'Monto insuficiente';
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
      <View style={styles.container}>
        <View style={styles.receipt}>
          <Text style={styles.header}>Restaurante XYZ</Text>
          <Text style={styles.subHeader}>Comprobante de Pago</Text>
          <View style={styles.divider} />
          <Text style={styles.text}>Pedido ID: {pedido.id_pedido}</Text>
          <Text style={styles.text}>Estado: {pedido.estado}</Text>
          <Text style={styles.text}>Total: ${pedido.total_cuenta}</Text>
          <Text style={styles.text}>Hora del Pedido: {pedido.hora}</Text>
          <View style={styles.divider} />
          <Text style={styles.text}>Forma de Pago:</Text>
          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={formaPago}
              style={styles.picker}
              onValueChange={(itemValue) => setFormaPago(itemValue)}
            >
              <Picker.Item label="Seleccione una forma de pago" value="" />
              <Picker.Item label="Efectivo" value="efectivo" />
              <Picker.Item label="Tarjeta" value="tarjeta" />
            </Picker>
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
                onPress={() => Alert.alert('Pago', 'El pago en efectivo ha sido realizado.')}
              />
            </View>
          )}
          {formaPago === 'tarjeta' && (
            <Button
              title="Pagar con PayPal"
              onPress={() => Alert.alert('Pago', 'Redirigiendo a PayPal...')}
            />
          )}
        </View>
      </View>
    </TouchableWithoutFeedback>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#fbf8f3',
    justifyContent: 'center',
    alignItems: 'center',
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
  pickerContainer: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 5,
    marginBottom: 20,
    overflow: 'hidden',
  },
  picker: {
    height: 50,
    width: '100%',
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
});

export default PagoScreen;