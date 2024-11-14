import React, { useEffect, useState, useRef } from 'react';
import { View, Text, FlatList, TextInput, Button, StyleSheet, TouchableOpacity, KeyboardAvoidingView, Platform, Image } from 'react-native';
import axios from 'axios';
import { getApiUrl } from '../utils/api';

const Chat = ({ route, navigation }: { route: any, navigation: any }) => {
  const { id_usuario } = route.params;

  interface Message {
    id: number;
    usuario_envia: string;
    mensaje: string;
    id_usuario_envia: number; // Asegúrate de tener este campo en la interfaz
  }

  interface User {
    id_usuario: number;
    nombre_usuario: string;
    foto_perfil?: string; // Añadir campo opcional para la foto de perfil
    lastMessage?: Message; // Añadir campo opcional para el último mensaje
  }

  const [messages, setMessages] = useState<Message[]>([]);
  const [message, setMessage] = useState('');
  const [users, setUsers] = useState<User[]>([]);
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  const flatListRef = useRef<FlatList>(null);

  const fetchMessages = () => {
    if (selectedUser) {
      const url = getApiUrl(`modulos/cargar_mensajes_app.php?id_usuario=${id_usuario}&id_destinatario=${selectedUser.id_usuario}`);
      console.log(`Fetching messages from: ${url}`);
      axios.get(url)
        .then(response => {
          const data = response.data as User[];
          console.log('Messages fetched:', response.data); // Verifica los datos recibidos
          setMessages(response.data as Message[]);
          flatListRef.current?.scrollToEnd({ animated: true });
        })
        .catch(error => console.error('Error fetching messages:', error));
    }
  };

  const fetchUsers = () => {
    const url = getApiUrl('modulos/cargar_usuarios.php');
    console.log(`Fetching users from: ${url}`);
    axios.get(url)
      .then(response => {
        console.log('Users fetched:', response.data);
        const usersWithLastMessage = response.data.map(async (user: User) => {
          const lastMessage = await fetchLastMessage(user.id_usuario);
          return { ...user, lastMessage };
        });
        Promise.all(usersWithLastMessage).then(setUsers);
      })
      .catch(error => console.error('Error fetching users:', error));
  };

  const fetchLastMessage = (userId: number) => {
    const url = getApiUrl(`modulos/cargar_ultimo_mensaje.php?id_usuario=${id_usuario}&id_destinatario=${userId}`);
    return axios.get(url)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching last message:', error);
        return null;
      });
  };

  const sendMessage = () => {
    if (selectedUser) {
      if (message.trim() === '') {
        alert('No se puede enviar un mensaje vacío.');
        return;
      }
      const url = getApiUrl('modulos/enviar_mensaje_app.php');
      const payload = new URLSearchParams({
        id_usuario_envia: id_usuario.toString(),
        id_destinatario: selectedUser.id_usuario.toString(),
        mensaje: message,
      });
      console.log('Sending message with payload:', payload.toString());
      axios.post(url, payload.toString(), {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
      })
        .then(response => {
          console.log('Message sent:', response.data);
          setMessage('');
          fetchMessages();
        })
        .catch(error => console.error('Error sending message:', error));
    } else {
      alert('Por favor, selecciona un usuario para enviar el mensaje.');
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  useEffect(() => {
    fetchMessages();
  }, [selectedUser]);

  useEffect(() => {
    const interval = setInterval(() => {
      fetchMessages();
      fetchUsers(); // Actualiza los usuarios y sus últimos mensajes
    }, 5000); // Intervalo de 5 segundos

    return () => clearInterval(interval); // Limpiar el intervalo al desmontar el componente
  }, [selectedUser]);

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      keyboardVerticalOffset={90}
    >
      {selectedUser ? (
        <View style={styles.chatContainer}>
          <View style={styles.header}>
            <TouchableOpacity style={styles.backButton} onPress={() => setSelectedUser(null)}>
              <Text style={styles.backButtonText}>←</Text>
            </TouchableOpacity>
            {selectedUser.foto_perfil ? (
              <Image source={{ uri: selectedUser.foto_perfil }} style={styles.profileImage} />
            ) : (
              <View style={styles.defaultProfileImage}>
                <Text style={styles.defaultProfileText}>U</Text>
              </View>
            )}
            <Text style={styles.userName}>{selectedUser.nombre_usuario}</Text>
          </View>
          <FlatList
            ref={flatListRef}
            data={messages}
            keyExtractor={(item) => item.id?.toString() || Math.random().toString()}
            renderItem={({ item }) => {
              const isSentByCurrentUser = Number(item.id_usuario_envia) === Number(id_usuario);
              console.log(`Message ID: ${item.id}, Sent by: ${item.id_usuario_envia}, Current User: ${id_usuario}, Position: ${isSentByCurrentUser ? 'Derecha' : 'Izquierda'}`);
              return (
                <View style={[styles.messageContainer, isSentByCurrentUser ? styles.sentMessage : styles.receivedMessage]}>
                  <Text style={styles.messageText}>{item.mensaje}</Text>
                </View>
              );
            }}
            onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
          />
          <View style={styles.inputContainer}>
            <TextInput
              style={styles.input}
              value={message}
              onChangeText={setMessage}
              placeholder="Escribe un mensaje"
              placeholderTextColor="#888"
            />
            <Button title="Enviar" onPress={sendMessage} />
          </View>
        </View>
      ) : (
        <View style={styles.userListContainer}>
          <Text style={styles.heading}>Usuarios</Text>
          <FlatList
            data={users}
            keyExtractor={(item) => item.id_usuario.toString()}
            renderItem={({ item }) => (
              <TouchableOpacity
                style={styles.userItem}
                onPress={() => setSelectedUser(item)}
              >
                {item.foto_perfil ? (
                  <Image source={{ uri: item.foto_perfil }} style={styles.profileImage} />
                ) : (
                  <View style={styles.defaultProfileImage}>
                    <Text style={styles.defaultProfileText}>U</Text>
                  </View>
                )}
                <View style={styles.userInfo}>
                  <Text style={styles.userText}>{item.nombre_usuario}</Text>
                  {item.lastMessage && <Text style={styles.lastMessage}>{item.lastMessage.mensaje}</Text>}
                </View>
              </TouchableOpacity>
            )}
          />
        </View>
      )}
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5dc', // Color crema
  },
  userListContainer: {
    flex: 1,
    padding: 16,
    backgroundColor: '#333', // Fondo oscuro
  },
  chatContainer: {
    flex: 1,
    padding: 16,
    paddingBottom: 20, // Agrega un margen inferior
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(0, 0, 0, 0.5)', // Fondo oscuro con transparencia
    padding: 10,
    borderRadius: 10,
    marginBottom: 10,
  },
  backButton: {
    marginRight: 10,
  },
  backButtonText: {
    color: '#fff',
    fontSize: 24,
  },
  profileImage: {
    width: 40,
    height: 40,
    borderRadius: 20,
    marginRight: 10,
  },
  defaultProfileImage: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#ccc',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  defaultProfileText: {
    color: '#fff',
    fontSize: 18,
  },
  userName: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  heading: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#fff', // Color blanco para el texto del encabezado
  },
  userItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#ccc',
  },
  userInfo: {
    marginLeft: 10,
  },
  userText: {
    color: '#fff', // Color blanco para el texto del usuario
    fontSize: 16,
    fontWeight: 'bold',
  },
  lastMessage: {
    color: '#ccc', // Color gris claro para el último mensaje
    fontSize: 14,
  },
  messageContainer: {
    padding: 10,
    borderRadius: 20,
    marginVertical: 5,
    maxWidth: '80%',
  },
  sentMessage: {
    alignSelf: 'flex-end',
    backgroundColor: '#FFF', // Fondo blanco para los mensajes enviados
    borderTopRightRadius: 0,
    borderTopLeftRadius: 20,
    borderBottomRightRadius: 20,
    borderBottomLeftRadius: 20,
    marginLeft: 'auto', // Alinea los mensajes enviados a la derecha
  },
  receivedMessage: {
    alignSelf: 'flex-start',
    backgroundColor: '#FFF', // Fondo blanco para los mensajes recibidos
    borderTopRightRadius: 20,
    borderTopLeftRadius: 20,
    borderBottomRightRadius: 20,
    borderBottomLeftRadius: 0,
    marginRight: 'auto', // Alinea los mensajes recibidos a la izquierda
  },
  messageText: {
    color: '#333',
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#ccc',
    padding: 10,
    marginBottom: 60, // Agrega un margen inferior
  },
  input: {
    flex: 1,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 20,
    padding: 10,
    marginRight: 10,
    color: '#333',
  },
});

export default Chat;