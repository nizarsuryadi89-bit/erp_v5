<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Generic adapter contract, not a WhatsApp provider-specific API. */
class Complaint_webhook_verifier
{
    public function verify($body, $source, $timestamp, $signature, $sources, $now = NULL)
    {
        if (!is_string($body) || strlen($body) > 65536 || !is_string($source) || !isset($sources[$source])) {
            throw new InvalidArgumentException('Webhook tidak valid.');
        }
        $mapping = $sources[$source];
        $now = $now === NULL ? time() : $now;
        if (!is_string($timestamp) || !preg_match('/^\d{10}$/D', $timestamp) || abs($now - (int) $timestamp) > 300 ||
            !is_string($signature) || !preg_match('/^[a-f0-9]{64}$/D', $signature) ||
            empty($mapping['secret']) || strlen($mapping['secret']) < 32 || empty($mapping['perusahaan_id']) ||
            !hash_equals(hash_hmac('sha256', $source."\n".$timestamp."\n".$body, $mapping['secret']), $signature)) {
            throw new InvalidArgumentException('Autentikasi webhook gagal.');
        }
        $p = json_decode($body, TRUE);
        if (!is_array($p)) { throw new InvalidArgumentException('Payload harus JSON.'); }
        foreach (array('message_id' => 191, 'chat_id' => 191, 'sender' => 191, 'sent_at' => 40, 'text' => 16000) as $key => $limit) {
            if (!isset($p[$key]) || !is_string($p[$key]) || trim($p[$key]) === '' || strlen($p[$key]) > $limit || !preg_match('//u', $p[$key])) {
                throw new InvalidArgumentException('Payload pesan tidak lengkap.');
            }
        }
        if (empty($mapping['allowed_chats']) || !in_array($p['chat_id'], $mapping['allowed_chats'], TRUE)) {
            throw new InvalidArgumentException('Percakapan tidak diizinkan.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:Z|[+-](?:0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])$/D', $p['sent_at'])) {
            throw new InvalidArgumentException('Waktu pesan harus RFC3339.');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', $p['sent_at']);
        $errors = DateTimeImmutable::getLastErrors();
        if (!$date || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
            throw new InvalidArgumentException('Waktu pesan tidak valid.');
        }
        return array('company' => (int) $mapping['perusahaan_id'], 'source' => $source,
            'key' => hash('sha256', 'webhook'."\n".$source."\n".$p['message_id']),
            'conversation' => $p['chat_id'], 'sender' => $p['sender'],
            'sent_at' => $date->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('Y-m-d H:i:s'), 'body' => $p['text']);
    }
}
