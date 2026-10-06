<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** WhatsApp Android/iOS TXT exports. Date ordering is explicit, never guessed. */
class Whatsapp_export_parser
{
    public function parse($text, $date_order = 'DMY', $limit = 500)
    {
        if (!in_array($date_order, array('DMY', 'MDY'), TRUE)) {
            throw new InvalidArgumentException('Urutan tanggal tidak valid.');
        }
        if (!is_string($text) || !preg_match('//u', $text)) {
            throw new InvalidArgumentException('File harus berupa teks UTF-8.');
        }
        $text = preg_replace('/[\x{FEFF}\x{200E}\x{200F}\x{202A}-\x{202E}]/u', '', $text);
        $text = str_replace(array("\r\n", "\r", "\xC2\xA0", "\xE2\x80\xAF"), array("\n", "\n", ' ', ' '), $text);
        $messages = array();
        $current = NULL;
        $system = 0;
        $header = '/^\[?(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{2,4}),?\s+(\d{1,2})[:.](\d{2})(?:[:.](\d{2}))?\s*(AM|PM)?\]?\s*(?:-\s*)?(.+)$/i';
        foreach (explode("\n", $text) as $line) {
            if (preg_match($header, $line, $m)) {
                if ($current) {
                    $messages[] = $current;
                    $current = NULL;
                }
                if (!preg_match('/^([^:]+):\s?(.*)$/', $m[8], $content)) {
                    $system++;
                    continue;
                }
                $year = (int) $m[3];
                if (strlen($m[3]) === 2) { $year += 2000; }
                $day = (int) $m[$date_order === 'DMY' ? 1 : 2];
                $month = (int) $m[$date_order === 'DMY' ? 2 : 1];
                $hour = (int) $m[4];
                $minute = (int) $m[5];
                $second = isset($m[6]) && $m[6] !== '' ? (int) $m[6] : 0;
                $ampm = isset($m[7]) ? strtoupper($m[7]) : '';
                if ($year < 1000 || !checkdate($month, $day, $year) || $minute > 59 || $second > 59 ||
                    ($ampm && ($hour < 1 || $hour > 12)) || (!$ampm && $hour > 23)) {
                    throw new InvalidArgumentException('Tanggal/jam chat tidak valid. Periksa pilihan DMY atau MDY.');
                }
                if ($ampm) { $hour = ($hour % 12) + ($ampm === 'PM' ? 12 : 0); }
                $current = array('sender' => trim($content[1]),
                    'sent_at' => sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second),
                    'body' => $content[2]);
            } elseif (preg_match('/^\[?\d{1,4}[\/.-]\d{1,2}[\/.-]\d{1,4}.*\d[:.]\d{2}/', $line)) {
                throw new InvalidArgumentException('Ada format tanggal chat yang tidak didukung. Tidak ada pesan diimpor.');
            } elseif ($current !== NULL) {
                $current['body'] .= "\n".$line;
            } elseif (trim($line) !== '') {
                // Do not silently attach an unrecognized timestamp to another message.
                if (preg_match('/^\[?\d{1,4}[\/.-]\d/', $line)) {
                    throw new InvalidArgumentException('Format tanggal chat tidak didukung. Gunakan ekspor WhatsApp Android/iOS.');
                }
            }
            if (count($messages) > $limit) {
                throw new InvalidArgumentException('Maksimal '.$limit.' pesan per impor. Bagi file menjadi beberapa bagian.');
            }
        }
        if ($current) { $messages[] = $current; }
        $messages = array_values(array_filter($messages, function ($m) { return trim($m['body']) !== ''; }));
        if (!$messages || count($messages) > $limit) {
            throw new InvalidArgumentException('Tidak ada chat terbaca atau batas '.$limit.' pesan terlampaui.');
        }
        foreach ($messages as &$message) {
            $message['body'] = trim($message['body']);
            if ($message['sender'] === '' || strlen($message['sender']) > 191 || strlen($message['body']) > 16000) {
                throw new InvalidArgumentException('Nama pengirim atau panjang pesan melampaui batas.');
            }
        }
        unset($message);
        return array('messages' => $messages, 'system_messages' => $system);
    }
}
