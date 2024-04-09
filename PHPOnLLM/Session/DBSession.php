<?php
namespace PHPOnLLM\Session;

class DBSession implements \SessionHandlerInterface
{
    private $pdo;
    private $table;

    private function __construct(array $config)
    {
        if (!isset($config['type'], $config['dsn'], $config['username'], $config['password'], $config['table']) || $config['type'] !== 'mysql') {
            throw new InvalidArgumentException('Invalid configuration settings');
        }

        // Initialize database connection
        $this->pdo = new PDO($config['dsn'], $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        $this->table = $config['table'];

        // Set object as the session handler
        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );

        // Autostart session
        session_start();
    }

    public static function start(array $config)
    {
        new self($config);
    }

    public function open($savePath, $sessionName)
    {
        return true; // No action needed here
    }

    public function close()
    {
        return true; // No action needed here
    }

    public function read($id)
    {
        $sql = "SELECT session_data FROM {$this->table} WHERE session_id = :session_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':session_id', $id, PDO::PARAM_STR);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['session_data'];
        }

        return ''; // Return empty string if no session data found
    }

    public function write($id, $data)
    {
        $expiration = time() + (int) ini_get('session.gc_maxlifetime');
        $sql = "REPLACE INTO {$this->table} (session_id, session_data, session_expiration) VALUES (:session_id, :session_data, :expiration)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':session_id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':session_data', $data, PDO::PARAM_STR);
        $stmt->bindParam(':expiration', $expiration, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function destroy($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = :session_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':session_id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function gc($maxlifetime)
    {
        $past = time() - $maxlifetime;
        $sql = "DELETE FROM {$this->table} WHERE session_expiration < :past";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':past', $past, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function clear()
    {
        // Cleanup session variables
        $_SESSION = [];

        // Destroy the session
        if (session_id()) {
            session_destroy();
        }
    }
}
