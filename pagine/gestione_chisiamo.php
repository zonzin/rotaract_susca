<?php
session_start();

// Controllo sicurezza: Se non è admin, rimanda al login
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: accedi.php");
    exit;
}

// PERCORSI FILE JSON (Devono essere nella stessa cartella 'pagine')
$filePresidenti = 'presidenti.json';
$fileDirettivo = 'direttivo.json';

$msg = "";

// --- LOGICA SALVATAGGIO NUOVO PRESIDENTE ---
if (isset($_POST['add_presidente'])) {
    $anno = $_POST['anno'];
    $nome = $_POST['nome'];
    
    if (!empty($anno) && !empty($nome)) {
        $dati = file_exists($filePresidenti) ? json_decode(file_get_contents($filePresidenti), true) : [];
        $dati[] = ['anno' => $anno, 'nome' => $nome];
        
        // Ordina per anno decrescente (opzionale)
        // usort($dati, function($a, $b) { return strcmp($b['anno'], $a['anno']); });

        file_put_contents($filePresidenti, json_encode($dati, JSON_PRETTY_PRINT));
        $msg = "Presidente aggiunto con successo!";
    }
}

// --- LOGICA AGGIORNAMENTO DIRETTIVO ---
if (isset($_POST['update_direttivo'])) {
    $ruoli = $_POST['ruolo']; 
    $nomi = $_POST['nome_direttivo'];
    
    $nuovoDirettivo = [];
    for($i=0; $i < count($ruoli); $i++) {
        if(!empty($nomi[$i])) {
            $nuovoDirettivo[] = [
                'ruolo' => $ruoli[$i],
                'nome' => $nomi[$i]
            ];
        }
    }
    file_put_contents($fileDirettivo, json_encode($nuovoDirettivo, JSON_PRETTY_PRINT));
    $msg = "Direttivo aggiornato con successo!";
}

// Leggiamo i dati attuali
$direttivoAttuale = file_exists($fileDirettivo) ? json_decode(file_get_contents($fileDirettivo), true) : [
    ['ruolo' => 'Presidente', 'nome' => ''],
    ['ruolo' => 'Vice-Presidente', 'nome' => ''],
    ['ruolo' => 'Segretario', 'nome' => ''],
    ['ruolo' => 'Tesoriere', 'nome' => ''],
    ['ruolo' => 'Prefetto', 'nome' => ''],
    ['ruolo' => 'Past President', 'nome' => '']
];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Chi Siamo</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        body { padding-top: 150px; text-align: center; background-color: #f9f9f9; }
        .admin-panel { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: left; }
        input[type="text"] { width: 100%; padding: 8px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        label { font-weight: bold; color: #E2457C; }
        .msg-success { color: green; font-weight: bold; margin-bottom: 20px; text-align: center; }
        hr { border: 0; border-top: 1px solid #eee; margin: 30px 0; }
    </style>
</head>
<body>

    <div class="top-nav">
        <a href="../index.html">Home Sito</a>
        <a href="area_riservata.php" style="font-weight: bold;">⬅ Torna alla Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="admin-panel">
        <h2 style="text-align: center;">Modifica Pagina "Chi Siamo"</h2>
        
        <?php if($msg): ?>
            <p class="msg-success"><?= $msg ?></p>
        <?php endif; ?>

        <h3>1. Aggiungi Presidente all'Albo</h3>
        <form method="POST">
            <label>Anno Rotaract (es. 2025-2026)</label>
            <input type="text" name="anno" required placeholder="Inserisci l'anno...">
            
            <label>Nome e Cognome</label>
            <input type="text" name="nome" required placeholder="Inserisci il nome...">
            
            <button type="submit" name="add_presidente" class="round-btn" style="width:100%">Aggiungi alla lista</button>
        </form>

        <hr>

        <h3>2. Modifica Direttivo in Carica</h3>
        <p style="font-size: 0.9em; color: #666;">Modifica i nomi accanto ai ruoli e salva.</p>
        <form method="POST">
            <?php foreach($direttivoAttuale as $membro): ?>
                <div style="margin-bottom: 10px;">
                    <label><?= htmlspecialchars($membro['ruolo']) ?></label>
                    <input type="hidden" name="ruolo[]" value="<?= htmlspecialchars($membro['ruolo']) ?>">
                    <input type="text" name="nome_direttivo[]" value="<?= htmlspecialchars($membro['nome']) ?>">
                </div>
            <?php endforeach; ?>
            <button type="submit" name="update_direttivo" class="round-btn" style="width:100%">Aggiorna Direttivo</button>
        </form>
    </div>
    <br><br>
</body>
</html>