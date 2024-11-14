import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert } from 'react-native';
import axios from 'axios';
import { NavigationProp } from '@react-navigation/native';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL

const LoginScreen = ({ navigation }: { navigation: NavigationProp<any> }) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = () => {
    const url = getApiUrl('login/procesar_login_app.php');
    console.log('Datos de login:', { username, password }); // Agrega este log
    axios.post(url, {
      username: username,
      password: password
    }, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    })
    .then(response => {
      console.log('Respuesta del servidor:', response.data); // Agrega este log
      if (response.data.success) {
        navigation.navigate('MeseroList', { userId: response.data.user_id });
      } else {
        Alert.alert('Error', response.data.message);
      }
    })
    .catch(error => {
      console.error('Error al iniciar sesión:', error);
      if (error.response) {
        // El servidor respondió con un código de estado fuera del rango 2xx
        Alert.alert('Error del servidor', `Código de estado: ${error.response.status}\nMensaje: ${error.response.data}`);
      } else if (error.request) {
        // La solicitud fue hecha pero no se recibió respuesta
        Alert.alert('Error de red', `No se recibió respuesta del servidor. Verifica tu conexión a internet.\nDetalles: ${error.message}`);
      } else {
        // Algo sucedió al configurar la solicitud
        Alert.alert('Error de configuración', `Error al configurar la solicitud: ${error.message}`);
      }
    });
  };

  return (
    <View style={styles.container}>
      <View style={styles.loginContainer}>
        <Text style={styles.title}>Inicio de Sesión</Text>
        <TextInput
          style={styles.input}
          placeholder="Nombre de Usuario"
          value={username}
          onChangeText={setUsername}
        />
        <TextInput
          style={styles.input}
          placeholder="Contraseña"
          value={password}
          onChangeText={setPassword}
          secureTextEntry
        />
        <Button title="Iniciar Sesión" onPress={handleLogin} color="#5cb85c" />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f4f4f4',
  },
  loginContainer: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.1,
    shadowRadius: 10,
    maxWidth: 400,
    width: '100%',
    textAlign: 'center',
  },
  title: {
    marginBottom: 20,
    fontSize: 24,
    color: '#333',
  },
  input: {
    width: '100%',
    padding: 10,
    marginBottom: 20,
    borderColor: '#ddd',
    borderWidth: 1,
    borderRadius: 4,
  },
  button: {
    backgroundColor: '#5cb85c',
    padding: 10,
    borderRadius: 4,
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
  buttonHover: {
    backgroundColor: '#4cae4c',
  },
  errorMessage: {
    color: '#d9534f',
    marginBottom: 20,
  },
});

export default LoginScreen;