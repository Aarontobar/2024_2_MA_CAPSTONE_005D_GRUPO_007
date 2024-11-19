import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ImageBackground } from 'react-native';
import axios from 'axios';
import { NavigationProp } from '@react-navigation/native';
import { getApiUrl } from '../utils/api'; // Importa la función para obtener la URL
import { Ionicons } from '@expo/vector-icons'; // Asegúrate de tener @expo/vector-icons instalado
import { red } from 'react-native-reanimated/lib/typescript/Colors';

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
      const data = response.data as { success: boolean; user_id: string; message: string };
      console.log('Respuesta del servidor:', data); // Agrega este log
      if (data.success) {
        navigation.navigate('MesasAsignadas', { id: data.user_id });
      } else {
        Alert.alert('Error', data.message);
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
      <ImageBackground
        style={[styles.header, { height: 350, width: '100%' }]}// Ajusta el ángulo de rotación según sea necesario
      >
        <TouchableOpacity style={styles.clientButton} onPress={() => navigation.navigate('ClienteScreen')}>
          <Text style={styles.clientButtonText}>Soy Cliente</Text>
        </TouchableOpacity>
      </ImageBackground>
      <View style={styles.content}>
        <Text style={styles.title}>Restaurante</Text>
        <Text style={styles.subtitle}>Enter your login details</Text>
        <View style={styles.inputContainer}>
          <TextInput
            style={styles.input}
            placeholder="Username"
            value={username}
            onChangeText={setUsername}
          />
          <TextInput
            style={styles.input}
            placeholder="Password"
            value={password}
            onChangeText={setPassword}
            secureTextEntry
          />
        </View>
        <TouchableOpacity style={styles.button} onPress={handleLogin}>
          <Text style={styles.buttonText}>ENTER</Text>
        </TouchableOpacity>
        <View style={styles.divider}>
          <View style={styles.dividerLine} />
          <Text style={styles.dividerText}>OR ENTER WITH</Text>
          <View style={styles.dividerLine} />
        </View>
        <View style={styles.socialLogin}>
          <Ionicons name="logo-facebook" size={24} color="#808080" />
          <Ionicons name="logo-apple" size={24} color="#808080" />
          <Ionicons name="logo-snapchat" size={24} color="#808080" />
          <Ionicons name="logo-google" size={24} color="#808080" />
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#e3f5c6'
  },
  header: {
    height: 200,
    justifyContent: 'center',
    alignItems: 'center',
  },
  clientButton: {
    backgroundColor: '#9370DB',
    padding: 10,
    borderRadius: 5,
  },
  clientButtonText: {
    color: 'white',
    fontSize: 16,
  },
  content: {
    flex: 1,
    padding: 20,
    textAlign: 'center',
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
  },
  title: {
    fontFamily: 'Brush Script MT',
    fontSize: 36,
    color: '#2F4F4F',
    margin: 0,
  },
  subtitle: {
    fontSize: 14,
    color: '#808080',
    margin: 10,
  },
  inputContainer: {
    marginVertical: 20,
  },
  input: {
    width: '100%',
    padding: 10,
    borderColor: '#D3D3D3',
    borderWidth: 1,
    borderRadius: 5,
    fontSize: 16,
    color: '#808080',
    marginBottom: 10,
  },
  button: {
    backgroundColor: '#9370DB',
    padding: 10,
    borderRadius: 5,
    fontSize: 16,
    width: '100%',
    marginTop: 10,
  },
  buttonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 20,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: '#D3D3D3',
  },
  dividerText: {
    marginHorizontal: 10,
    color: '#808080',
  },
  socialLogin: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
  },
  gradientBackground: {
    flex: 1,
    backgroundColor: 'linear-gradient(45deg, red, orange)',
  },
});

export default LoginScreen;