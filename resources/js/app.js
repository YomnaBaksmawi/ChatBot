import './bootstrap';
import './echo';
window.Echo.private('conversation.2')
    .listen('BotMessageCreated', (event) => {
        console.log('BOT MESSAGE:', event);
    });