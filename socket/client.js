import { io } from "socket.io-client";

const socket = io({
  url: 'http://77.222.46.176',
  path: '/socket.io/',
  transports: ['websocket']
});

// Тестирование подключения
socket.on('connect', () => {
  console.log('Подключение успешно');
});

// Тестирование ошибок подключения
socket.on('connect_error', (err) => {
  console.log(`Ошибка подключения: ${err.message}`);
});

// Тестирование отправки и получения сообщений
socket.emit('chat', 'Test message');
socket.on('chat', (message) => {
  console.log(`Получено сообщение: ${message}`);
});