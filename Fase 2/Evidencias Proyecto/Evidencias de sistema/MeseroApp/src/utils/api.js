// src/utils/api.js
import config from '../config';

export const getApiUrl = (endpoint) => {
  return `http://${config.serverIp}:${config.serverPort}${config.basePath}${endpoint}`;
};