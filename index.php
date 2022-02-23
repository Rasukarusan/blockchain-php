<?php

require_once './Blockchain.php';

$blockchain = new Blockchain();
// $node_identifier = 'myuuid';
$node_identifier = uniqid();
$req_uri = $_SERVER['REQUEST_URI'];
$req_method = $_SERVER['REQUEST_METHOD'];

switch ($req_uri) {
    case '/transactions/new':
        break;
    case '/mine':
        break;
    case '/chain':
        break;
    default:
        echo 'not found';
        break;
}

function newTransaction(): void
{
    $sender = $_POST['sender'];
    $recipient = $_POST['recipient'];
    $amount = $_POST['amount'];

    $blockchain = clone $blockchain;
    $index = $blockchain->newTransaction($sender, $recipient, $amount);
    echo json_encode(['message' => "Transaction will be added to Block ${index}"]);
}
function mine(): void
{
    $blockchain = clone $blockchain;
    $last_block = $blockchain->lastBlock();
    $last_proof = $last_block['proof'];
    $proof = $blockchain->proofOfWork($last_proof);

    // マイナーに報酬を与える
    // senderに'0'をセットすることで新規コインであることを示す
    $sender = 0;
    $recipient = $node_identifier;
    $amount = 1;
    $blockchain->newTransaction($sender, $recipient, $amount);

    // チェーンに追加
    $previous_hash = $blockchain::createHash($last_block);
    $block = $blockchain->newBlock($proof, $previous_hash);
    $response = [
            'message' => 'New Block Forged',
            'index' => $block['index'],
            'transactions' => $block['transactions'],
            'proof' => $block['proof'],
            'previous_hash' => $block['previous_hash'],
        ];
    echo json_encode($response);
}

function chain(): void
{
    $blockchain = clone $blockchain;
    $response = [
            'chain' => $blockchain->chain,
            'length' => count($blockchain->chain),
        ];
    echo json_encode($response);
}

mine();
mine();
mine();
chain();
