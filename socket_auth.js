import express from 'express';
import http from 'http';
import { Server as socketIo } from 'socket.io';
import Redis from 'ioredis';
import axios from 'axios';
import cors from 'cors';
import { createAdapter } from '@socket.io/redis-adapter'; // Changed to use proper adapter

const app = express();
const server = http.createServer(app);

app.use(cors());

const io = new socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true
    }
});

// Redis configuration
const redisConfig = {
    host: 'localhost',
    port: 6379,
    password: 'simpuni_redis',
    db: 0,
};

// Create Redis pub/sub clients
const pubClient = new Redis(redisConfig);
const subClient = pubClient.duplicate();

// Set up Redis adapter
Promise.all([pubClient, subClient].map(client => client.ping()))
    .then(() => {
        io.adapter(createAdapter(pubClient, subClient));
        console.log('Redis adapter connected');
    })
    .catch(err => {
        console.error('Failed to connect to Redis:', err);
        process.exit(1);
    });

// Authentication middleware (unchanged)
io.use(async (socket, next) => {
    const token = socket.handshake.query.token;

    try {
        const response = await axios.get('http://89.116.20.101/api/me', {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        response.status === 200 ? next() : next(new Error('Authentication error'));
    } catch (error) {
        console.error('Authentication error:', error);
        next(new Error('Authentication error'));
    }
});

// Socket.IO connection handler
io.on('connection', (socket) => {
    console.log('User connected:', socket.id);


    // Join private room
    socket.on('join assignment', (roomId) => {
        // Validate room access here (e.g., check if user is allowed to join)
        socket.join(roomId);
        console.log(`User ${socket.id} joined room ${roomId}`);
    });

    // Leave private room
    socket.on('leave assignment', (roomId) => {
        socket.leave(roomId);
        console.log(`User ${socket.id} left room ${roomId}`);
    });

    // Private message handler
    socket.on('assignment', (data) => {
        // Data should contain { roomId: string, message: string }
        console.log(`Message received for room ${data.roomId}:`, data.message);

        // Broadcast to specific room
        io.to(data.roomId).emit('assignment', {
            sender: socket.id,
            message: data.message
        });
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

// Remove the old Redis pub/sub code and keep the rest below
app.get('/', (req, res) => {
    res.send('Socket.io server is running!');
});

server.listen(3000, () => {
    console.log('Server is running on port 3000');
});

// Error handling
pubClient.on('error', (err) => console.error('Redis pub error:', err));
subClient.on('error', (err) => console.error('Redis sub error:', err));
