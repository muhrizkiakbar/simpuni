import express from 'express';
import http from 'http';
import { Server as socketIo } from 'socket.io';  // Correct import
import Redis from 'ioredis';
import axios from 'axios';  // Used to send requests to your Laravel app

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

redis.psubscribe('*', (err, count) => {
    if (err) {
        console.error('Error subscribing to Redis channel:', err);
    } else {
        console.log(`Subscribed to ${count} channels`);
    }
});

redis.on('pmessage', (subscribed, channel, message) => {
    try {
        console.log(message)
        console.log(`Received message from channel: ${channel}`);
        message = JSON.parse(message); // Assuming the message is JSON-encoded
        // Emit to clients connected to socket.io
        io.emit(`${channel}:${message.event}`, message.data);
    } catch (error) {
        console.error('Error parsing Redis message:', error);
    }
});

io.use(async (socket, next) => {
    const token = socket.handshake.query.token;  // Get token from query string

    try {
        const response = await axios.post('group.restuguru.com/api/me', {}, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.status === 200) {
            // Token is valid, proceed with connection
            next();
        } else {
            // Invalid token, reject the connection
            next(new Error('Authentication error'));
        }
    } catch (error) {
        // If the verification request fails, reject the connection
        console.error('Authentication error:', error);
        next(new Error('Authentication error'));
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

