// app/index.tsx
import * as React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../../src/components/loginScreen';
import MeseroList from '../../src/components/MeseroList';
import MesasAsignadas from '../../src/components/MesaAsignada';
import MenuScreen from '../../src/components/MenuScreen';
import CartScreen from '../../src/components/CartScreen';
import Chat from '../../src/components/Chat';
import PagoScreen from '../../src/components/PagoScreen';

const Stack = createStackNavigator();

function App() {
  return (
    <Stack.Navigator initialRouteName="Login">
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="MeseroList" component={MeseroList} />
      <Stack.Screen name="MesasAsignadas" component={MesasAsignadas} />
      <Stack.Screen name="MenuScreen" component={MenuScreen} />
      <Stack.Screen name="CartScreen" component={CartScreen} />
      <Stack.Screen name="Chat" component={Chat} />
      <Stack.Screen name="PagoScreen" component={PagoScreen} />
    </Stack.Navigator>
  );
}

export default App;