<?php
require_once '../includes/auth.php';
require_once '../includes/ContactMessage.php';

// Verifica che l'utente sia admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$messages = new ContactMessage();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$totalMessages = $messages->getTotalMessages();
$totalPages = ceil($totalMessages / $perPage);

// Gestione delle azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['message_id'])) {
        $messageId = (int)$_POST['message_id'];
        
        switch ($_POST['action']) {
            case 'mark_read':
                $messages->updateStatus($messageId, 'read');
                break;
            case 'mark_replied':
                $messages->updateStatus($messageId, 'replied');
                break;
            case 'delete':
                $messages->delete($messageId);
                break;
        }
        
        // Redirect per evitare il riinvio del form
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=' . $page);
        exit;
    }
}

$messagesList = $messages->getAllMessages($page, $perPage);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Messaggi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php require_once 'includes/admin-header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Gestione Messaggi</h1>

            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Messaggio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php foreach ($messagesList as $message): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?php echo htmlspecialchars($message['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" 
                                       class="text-purple-400 hover:text-purple-300">
                                        <?php echo htmlspecialchars($message['email']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $message['status'] === 'new' ? 'bg-yellow-800 text-yellow-100' : 
                                                  ($message['status'] === 'read' ? 'bg-blue-800 text-blue-100' : 
                                                   'bg-green-800 text-green-100'); ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <div class="flex space-x-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="action" value="mark_read">
                                            <button type="submit" class="text-blue-400 hover:text-blue-300">
                                                <i class="far fa-eye mr-1"></i> Segna come letto
                                            </button>
                                        </form>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="action" value="mark_replied">
                                            <button type="submit" class="text-green-400 hover:text-green-300">
                                                <i class="far fa-check-circle mr-1"></i> Segna come risposto
                                            </button>
                                        </form>
                                        <form method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo messaggio?');">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="text-red-400 hover:text-red-300">
                                                <i class="far fa-trash-alt mr-1"></i> Elimina
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($messagesList)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                    Nessun messaggio trovato
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-700 
                                  <?php echo $i === $page ? 'bg-gray-700 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'; ?>
                                  text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html> 