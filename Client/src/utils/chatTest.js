// Test file to verify real-time chat functionality
import echo from '@/plugins/echo';

export const testRealTimeChat = () => {
    console.log('Testing real-time chat connection...');

    // Test connecting to a channel
    const channel = echo.channel('consultation.1');

    channel.listen('.message', (data) => {
        console.log('Received test message:', data);
    });

    channel.subscribed(() => {
        console.log('Successfully connected to test channel');
    });

    channel.error((error) => {
        console.error('Test channel error:', error);
    });

    return channel;
};

export default testRealTimeChat;
