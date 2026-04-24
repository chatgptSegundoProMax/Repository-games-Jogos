<?php
// --- PONTE DE COMUNICAÇÃO (SERVER-SIDE) ---
// Esta parte trata a comunicação com a Groq sem erros de CORS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $apiKey = $input['apiKey'] ?? '';
    
    if (!empty($apiKey) && isset($input['messages'])) {
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $input['messages'],
            'temperature' => 0.7
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            echo $response;
        }
        curl_close($ch);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kratos Elétrico - Groq Edition</title>
  <style>
    /* O Teu Estilo Visual Original Reconstruído */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Nunito:wght@400;600&display=swap');

    body { background: #0a0a1a; display: flex; flex-direction: column; align-items: center; min-height: 100vh; font-family: 'Nunito', sans-serif; padding: 20px; color: #e0eaff; }

    /* Card de Configuração no Topo */
    .api-card { background: rgba(26, 26, 46, 0.9); border: 1px solid #f0a500; border-radius: 12px; padding: 15px; width: 100%; max-width: 480px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
    .api-card label { color: #f0a500; font-family: 'Cinzel'; font-size: 11px; display: block; margin-bottom: 5px; }
    .api-group { display: flex; gap: 10px; }
    .api-group input { flex: 1; background: #0f3460; border: 1px solid #1e4a8a; border-radius: 6px; padding: 8px; color: white; outline: none; }
    .api-group button { background: #f0a500; border: none; padding: 8px 15px; border-radius: 6px; font-family: 'Cinzel'; font-weight: bold; cursor: pointer; }

    /* Estrutura do Chat */
    .kb-wrap { width: 100%; max-width: 480px; height: 550px; background: #1a1a2e; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.4); border-bottom: 4px solid #f0a500; }
    .kb-header { background: #16213e; padding: 15px; border-bottom: 2px solid #f0a500; display: flex; align-items: center; gap: 12px; }
    .kb-avatar { width: 45px; height: 45px; background: #f0a500; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    
    .kb-messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #16213e; }
    .kb-msg { max-width: 85%; padding: 10px 14px; border-radius: 14px; font-size: 14px; line-height: 1.5; }
    .kb-msg.bot { background: #0f3460; align-self: flex-start; border-left: 3px solid #f0a500; border-bottom-left-radius: 2px; }
    .kb-msg.user { background: #f0a500; color: #1a1a2e; align-self: flex-end; font-weight: bold; border-bottom-right-radius: 2px; }

    .kb-input-area { padding: 15px; background: #1a1a2e; display: flex; gap: 10px; }
    #kb-input { flex: 1; background: #0f3460; border: 1px solid #f0a500; border-radius: 20px; padding: 10px 15px; color: white; outline: none; }
    #kb-send { background: #f0a500; border: none; padding: 10px 20px; border-radius: 20px; font-family: 'Cinzel'; font-weight: bold; cursor: pointer; }
  </style>
</head>
<body>

  <div class="api-card">
    <label>CHAVE DO OLIMPO (GROQ API KEY)</label>
    <div class="api-group">
      <input type="password" id="api-key" placeholder="gsk_...">
      <button onclick="saveKey()">FORJAR</button>
    </div>
  </div>

  <div class="kb-wrap">
    <header class="kb-header">
      <div class="kb-avatar">⚡</div>
      <div>
        <p style="font-family: 'Cinzel'; color: #f0a500; font-size: 14px;">KRATOS ELÉTRICO</p>
        <p style="font-size: 10px; color: #7ecfff;">ONLINE NO OLIMPO</p>
      </div>
    </header>

    <div class="kb-messages" id="chat-win">
      <div class="kb-msg bot">Mortal! Eu sou Kratos. Configura a tua chave acima para que possamos esmagar as tuas dúvidas elétricas!</div>
    </div>

    <div class="kb-input-area">
      <input type="text" id="kb-input" placeholder="O que queres saber?">
      <button onclick="sendMsg()">ENVIAR</button>
    </div>
  </div>

  <script>
    let API_KEY = "";
    const msgs = [{ role: 'system', content: 'És o Kratos de God of War, agora um eletricista experiente. Fala de forma épica, ríspida, mas didática. Responde apenas sobre eletrónica.' }];

    function saveKey() {
      API_KEY = document.getElementById('api-key').value;
      alert("Chave sincronizada!");
    }

    async function sendMsg() {
      const input = document.getElementById('kb-input');
      const text = input.value.trim();
      if (!text || !API_KEY) return alert("Insere a chave e a tua dúvida, mortal!");

      addMsg(text, 'user');
      msgs.push({ role: 'user', content: text });
      input.value = '';

      try {
        const res = await fetch('index.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ apiKey: API_KEY, messages: msgs })
        });
        const data = await res.json();
        const reply = data.choices[0].message.content;
        addMsg(reply, 'bot');
        msgs.push({ role: 'assistant', content: reply });
      } catch (e) {
        addMsg("Os deuses do código falharam! Verifica a tua chave.", 'bot');
      }
    }

    function addMsg(t, type) {
      const win = document.getElementById('chat-win');
      const div = document.createElement('div');
      div.className = 'kb-msg ' + type;
      div.textContent = t;
      win.appendChild(div);
      win.scrollTop = win.scrollHeight;
    }
  </script>
</body>
</html>
