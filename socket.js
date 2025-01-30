import express from 'express';
import http from 'http';
import { Server as socketIo } from 'socket.io'; // Correct import
import Redis from 'ioredis';

const app = express();
const server = http.createServer(app); // Create an HTTP server
const io = new socketIo(server); // Initialize socket.io with the server

// Adjust Redis connection
const redis = new Redis({
    host: 'localhost',  // Redis host (adjust based on your environment)
    port: 6379,     // Default Redis port
    password: 'simpuni_redis', // Redis password (if set)
    db: 0,          // Optional: default Redis database
});

// Subscribe to Redis channels
redis.psubscribe('*', (err, count) => {
    if (err) {
        console.error('Error subscribing to Redis channel:', err);
    } else {
        console.log(`Subscribed to ${count} channels`);
    }
});

// Handle messages from Redis
redis.on('pmessage', (subscribed, channel, message) => {
    try {
        console.log(`Received message from channel: ${channel}`);
        message = JSON.parse(message); // Assuming the message is JSON-encoded
        // Emit to clients connected to socket.io
        io.emit(`${channel}:${message.event}`, message.data);
    } catch (error) {
        console.error('Error parsing Redis message:', error);
    }
});

// Set up a simple route to check if server is running
app.get('/', (req, res) => {
    res.send('Socket.io server is running!');
});

// Start HTTP server
server.listen(3000, () => {
    console.log('Server is running on port 3000');
});

// Handle Redis connection errors
redis.on('error', (err) => {
    console.error('Redis connection error:', err);
});

