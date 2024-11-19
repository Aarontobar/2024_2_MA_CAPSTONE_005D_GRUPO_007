import { Camera, CameraView } from "expo-camera";
import { createStackNavigator } from '@react-navigation/stack';
import { useNavigation } from "@react-navigation/native";
import {
  AppState,
  Platform,
  SafeAreaView,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import { Overlay } from "./Overlay";
import { useEffect, useRef } from "react";
import axios from "axios";
import { getApiUrl } from "../utils/api"; // Importa la función para obtener la URL
import { Ionicons } from "@expo/vector-icons"; // Importa Ionicons

const Stack = createStackNavigator();

const ClienteScreen = ({ route, navigation }: { route: any, navigation: any }) => {
  const qrLock = useRef(false);
  const appState = useRef(AppState.currentState);

  useEffect(() => {
    const subscription = AppState.addEventListener("change", (nextAppState) => {
      if (
        appState.current.match(/inactive|background/) &&
        nextAppState === "active"
      ) {
        qrLock.current = false;
      }
      appState.current = nextAppState;
    });

    return () => {
      subscription.remove();
    };
  }, []);

  const handleBarcodeScanned = async ({ data }: { data: string }) => {
    if (data && !qrLock.current) {
      qrLock.current = true;

      // Validate if the data is a valid table ID (a number)
      if (!isValidTableId(data)) {
        alert("Solo escanea el QR de tu mesa, por favor.");
        qrLock.current = false;
        return;
      }

      const tableId = data; // Assuming the QR code contains the table ID

      // Fetch waiter-table detail and order in a single request
      const result = await fetchWaiterTableDetailAndOrder(tableId);
      if (result) {
        const { pedido, id_detalle_mesero_mesa, id_usuario } = result;
        if (pedido) {
          // Log the order ID and waiter_table_detail_id
          console.log(`ID del Pedido: ${pedido.id_pedido}, ID del Detalle Mesero Mesa: ${id_detalle_mesero_mesa}`);
          // Navigate to the verPedido page with the order ID
          navigation.navigate('VerPedido', { id_pedido: pedido.id_pedido });
        } else {
          // Log the waiter_table_detail_id and navigate to the menu page
          console.log(`Navegando a la página del menú con ID del Detalle Mesero Mesa: ${id_detalle_mesero_mesa}, ID del Usuario: ${id_usuario}`);
          navigation.navigate('MenuScreenUser', { mesaId: tableId, userId: id_usuario });
        }
      } else {
        // Handle case where there is no active waiter-table detail
        alert("Esta mesa aún no está asignada, comunícate con un encargado.");
        qrLock.current = false;
      }
    }
  };

  const isValidTableId = (data: string) => {
    // Check if the data is a number
    return /^\d+$/.test(data);
  };

  const fetchWaiterTableDetailAndOrder = async (tableId: string) => {
    const url = getApiUrl(`modulos/cargar_detalle_y_pedido.php?table_id=${tableId}`);
    try {
      const response = await axios.get(url);
      console.log('Response:', response.data);
      return response.data;
    } catch (error) {
      console.error('Error fetching waiter-table detail and order:', error);
      return null;
    }
  };

  return (
    <SafeAreaView style={StyleSheet.absoluteFillObject}>
      <Stack.Screen
        options={{
          title: "Overview",
          headerShown: false,
        }}
      />
      {Platform.OS === "ios" ? <StatusBar hidden /> : null}
      <CameraView
        style={StyleSheet.absoluteFillObject}
        facing="back"
        onBarcodeScanned={handleBarcodeScanned}
      />
      <Overlay />
      <TouchableOpacity
        style={styles.backButton}
        onPress={() => navigation.goBack()}
      >
        <Ionicons name="arrow-back" size={24} color="white" />
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  backButton: {
    position: "absolute",
    top: 40,
    left: 20,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    borderRadius: 20,
    padding: 10,
  },
});

export default ClienteScreen;