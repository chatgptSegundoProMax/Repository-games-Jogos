<?php
// === PONTE (PROXY) PARA A GROQ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $apiKey = $input['apiKey'] ?? '';
    
    if (!empty($apiKey) && isset($input['messages'])) {
        $url = 'https://api.groq.com/openai/v1/chat/completions';
        $data = [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $input['messages'],
            'temperature' => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Kratos Elétrico - Groq Edition</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Nunito:wght@400;600&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a1a; display: flex; flex-direction: column; align-items: center; min-height: 100vh; font-family: 'Nunito', sans-serif; padding: 20px; gap: 20px; }
        
        /* Card de Configuração */
        .api-card { background: #1a1a2e; border: 1px solid #f0a500; border-radius: 12px; padding: 15px; width: 100%; max-width: 480px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .api-card label { color: #f0a500; font-family: 'Cinzel'; font-size: 11px; font-weight: bold; }
        .api-input-group { display: flex; gap: 10px; margin-top: 8px; }
        .api-card input { flex: 1; background: #0f3460; border: 1px solid #1e4a8a; border-radius: 6px; padding: 8px; color: white; outline: none; }
        .api-card button { background: #f0a500; border: none; padding: 8px 15px; border-radius: 6px; font-family: 'Cinzel'; font-weight: bold; cursor: pointer; }

        /* Chat */
        .kb-wrap { width: 100%; max-width: 480px; height: 500px; background: #16213e; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #0f3460; }
        .kb-header { background: #1a1a2e; padding: 15px; border-bottom: 3px solid #f0a500; display: flex; align-items: center; gap: 10px; }
        .kb-messages { flex: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 10px; }
        .msg { padding: 10px 14px; border-radius: 12px; font-size: 14px; max-width: 85%; line-height: 1.4; }
        .msg.bot { background: #0f3460; color: white; align-self: flex-start; border-left: 3px solid #f0a500; }
        .msg.user { background: #f0a500; color: #1a1a2e; align-self: flex-end; font-weight: bold; }
        .kb-input-area { padding: 15px; background: #1a1a2e; display: flex; gap: 10px; }
        #kb-input { flex: 1; background: #0f3460; border: 1px solid #f0a500; border-radius: 20px; padding: 10px 15px; color: white; outline: none; }
        #kb-send { background: #f0a500; border: none; padding: 10px 20px; border-radius: 20px; font-family: 'Cinzel'; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <div class="api-card">
        <label>CHAVE DO OLIMPO (GROQ API KEY)</label>
        <div class="api-input-group">
            <input type="password" id="api-key" placeholder="gsk_...">
            <button onclick="saveKey()">CONFIGURAR</button>
        </div>
    </div>

    <div class="kb-wrap">
        <header class="kb-header">
            <span style="font-size: 24px;">⚡</span>
            <div style="color: #f0a500; font-family: 'Cinzel'; font-weight: bold;">Kratos Elétrico</div>
        </header>
        <div class="kb-messages" id="msgs">
            <div class="msg bot">Mortal! Coloca a tua chave acima e diz-me qual circuito ousa desafiar a minha fúria!</div>
        </div>
        <div class="kb-input-area">
            <input type="text" id="kb-input" placeholder="Escreve aqui...">
            <button id="kb-send" onclick="sendMsg()">FORJAR</button>
        </div>
    </div>

    <script>
        let API_KEY = "";
        const history = [{ role: 'system', content: 'És o Kratos de God of War, especialista em eletrónica. Fala de forma épica e curta.' }];

        function saveKey() {
            API_KEY = document.getElementById('api-key').value;
            alert("Chave forjada no fogo de Hefesto!");
        }

        async function sendMsg() {
            const input = document.getElementById('kb-input');
            const text = input.value.trim();
            if (!text || !API_KEY) return alert("Falta a chave ou a pergunta, mortal!");

            addMsg(text, 'user');
            history.push({ role: 'user', content: text });
            input.value = '';

            const res = await fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ apiKey: API_KEY, messages: history })
            });

            const data = await res.json();
            const reply = data.choices[0].message.content;
            addMsg(reply, 'bot');
            history.push({ role: 'assistant', content: reply });
        }

        function addMsg(t, type) {
            const d = document.createElement('div');
            d.className = 'msg ' + type;
            d.textContent = t;
            document.getElementById('msgs').appendChild(d);
            document.getElementById('msgs').scrollTop = 9999;
        }
    </script>
</body>
</html>
