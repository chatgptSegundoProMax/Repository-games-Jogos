/**
 * KatronBot - Assistente de Eletrônica
 * Chatbot simples com respostas baseadas em palavras-chave
 */

class KatronBot {
    constructor() {
        this.windowChat = document.getElementById('chat-window');
        this.inputField = document.getElementById('user-input');
        this.sendBtn = document.getElementById('send-btn');
        this.clock = document.getElementById('clock');

        this.brain = [
            { key: 'bom dia', resp: 'Bom dia! Em que posso ajudar com eletrônica hoje?' },
            { key: 'boa tarde', resp: 'Boa tarde! Precisa de ajuda com algum circuito?' },
            { key: 'nome', resp: 'Meu nome é ElectroBot, seu assistente virtual de eletrônica.' },
            { key: 'resistor', resp: 'Resistores são usados para limitar corrente em um circuito.' },
            { key: 'capacitor', resp: 'Capacitores armazenam energia elétrica temporariamente.' },
            { key: 'transistor', resp: 'Transistores funcionam como chave ou amplificador de sinal.' },
            { key: 'diodo', resp: 'Diodos permitem a passagem de corrente em apenas um sentido.' },
            { key: 'led', resp: 'LEDs são diodos emissores de luz. Não esqueça do resistor em série!' },
            { key: 'arduino', resp: 'Arduino é uma plataforma de prototipagem eletrônica muito popular.' },
            { key: 'tensao', resp: 'Tensão é a diferença de potencial elétrico medida em volts (V).' },
            { key: 'corrente', resp: 'Corrente elétrica é o fluxo de elétrons, medida em ampères (A).' },
            { key: 'ohm', resp: 'A Lei de Ohm diz: V = R * I (tensão = resistência x corrente).' },
            { key: 'curto', resp: 'Cuidado! Curto-circuito pode danificar componentes e causar riscos.' },
            { key: 'fonte', resp: 'Fontes de alimentação convertem energia para alimentar circuitos.' },
            { key: 'multimetro', resp: 'Multímetro mede tensão, corrente e resistência.' },
            { key: 'solda', resp: 'Use ferro de solda adequado e estanho para conexões seguras.' },
            { key: 'placa', resp: 'PCB é uma placa de circuito impresso onde os componentes são montados.' },
            { key: 'projeto', resp: 'Posso ajudar você a montar ou entender seu projeto eletrônico!' },
            { key: 'erro', resp: 'Verifique conexões, polaridade e alimentação do circuito.' },
            { key: 'contato', resp: 'Entre em contato para suporte técnico em eletrônica.' }
        ];

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateTime();
        setInterval(() => this.updateTime(), 60000);
    }

    setupEventListeners() {
        this.sendBtn.addEventListener('click', () => this.handleSendMessage());
        this.inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSendMessage();
            }
        });
    }

    handleSendMessage() {
        const text = this.inputField.value.trim();
        if (!text) return;

        this.appendMessage(text, 'user');
        this.inputField.value = '';
        this.inputField.focus();

        this.showLoadingIndicator();
        setTimeout(() => {
            this.removeLoadingIndicator();
            const response = this.findResponse(text);
            this.appendMessage(response, 'bot');
        }, 600);
    }

    findResponse(userInput) {
        const cleanInput = userInput.toLowerCase();
        const match = this.brain.find(item => cleanInput.includes(item.key));
        return match?.resp || 'Desculpe, não entendi. Tente termos como: resistor, capacitor, tensão ou Arduino.';
    }

    appendMessage(text, side) {
        const article = document.createElement('article');
        article.className = `msg ${side}`;
        article.textContent = text;
        this.windowChat.appendChild(article);
        this.scrollToBottom();
    }

    showLoadingIndicator() {
        const loading = document.createElement('article');
        loading.className = 'msg bot';
        loading.id = 'loading-indicator';
        loading.textContent = '...';
        this.windowChat.appendChild(loading);
        this.scrollToBottom();
    }

    removeLoadingIndicator() {
        const loading = document.getElementById('loading-indicator');
        if (loading) loading.remove();
    }

    scrollToBottom() {
        this.windowChat.scrollTop = this.windowChat.scrollHeight;
    }

    updateTime() {
        const now = new Date();
        this.clock.textContent = now.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Inicializar bot quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new KatronBot();
});
