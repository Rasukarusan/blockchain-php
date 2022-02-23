<?php

class Blockchain
{
    public $chain;

    private $current_transactions;

    public function __construct()
    {
        $this->chain = [];
        $this->current_transactions = [];
        $this->newBlock(1, 100);
    }

    /**
     * 新規ブロックの作成とチェーンに追加.
     * @param int         $proof
     * @param null|string $previous_hash
     * @return array 新規ブロック
     */
    public function newBlock(int $proof, string $previous_hash = null): array
    {
        $block = [
            'index' => count($this->chain) + 1,
            'timestamp' => time(),
            'transactions' => $this->current_transactions,
            'proof' => $proof,
            'previous_hash' => $previous_hash ?? $this->createHash(self::lastBlock()),
        ];

        // トランザクションをリセット
        $this->current_transactions = [];

        $this->chain[] = $block;
        return $block;
    }

    /**
     * トランザクションを追加.
     *
     * @param string $sender    送信者のアドレス
     * @param string $recipient 受信者のアドレス
     * @param int    $amount    量
     * @return int 最後のブロックの次のインデックス
     */
    public function newTransaction(string $sender, string $recipient, int $amount): int
    {
        $this->current_transactions[] = [
            'sender' => $sender,
            'recipient' => $recipient,
            'amount' => $amount,
        ];
        return self::lastBlock()['index'] + 1;
    }

    /**
     * プルーフオブワーク.
     *
     * @param int $last_proof
     * @return int
     */
    public function proofOfWork(int $last_proof): int
    {
        $proof = 0;
        while (self::validProof($last_proof, $proof)) {
            $proof++;
        }
        return $proof;
    }

    /**
     * ブロックのSHA-256ハッシュを生成.
     *
     * @param array $block
     * @return string SHA-256ハッシュ
     */
    public static function createHash($block): string
    {
        $block_string = json_encode($block);
        return hash('sha256', $block_string);
    }

    /**
     * チェーンの最後のブロックを返す.
     * @return array 最後のブロック
     */
    public function lastBlock(): array
    {
        var_dump($this->chain);
        return $this->chain[count($this->chain) - 1];
    }

    /**
     * プルーフのバリデーション.
     *
     * @param int $last_proof
     * @param int $proof
     * @return bool true: 正、false: 誤
     */
    private static function validProof(int $last_proof, int $proof): bool
    {
        $guess = "{$last_proof}{$proof}";
        $guess_hash = hash('sha256', $guess);
        return substr($guess_hash, -4) === '0000';
    }
}
