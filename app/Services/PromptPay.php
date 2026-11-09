<?php

namespace App\Services;

use InvalidArgumentException;

class PromptPay
{
    public function payload(string $identifier, float $amount): string
    {
        $digits = preg_replace('/\D+/', '', $identifier) ?? '';
        if (str_starts_with($digits, '66') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $account = $this->tlv('01', '0066'.substr($digits, 1));
        } elseif (strlen($digits) === 13) {
            $account = $this->tlv('02', $digits);
        } else {
            throw new InvalidArgumentException('Nomor PromptPay harus berupa nomor HP Thailand atau ID 13 digit.');
        }

        if ($amount <= 0 || $amount >= 100000000) {
            throw new InvalidArgumentException('Nominal PromptPay tidak valid.');
        }

        $merchant = $this->tlv('00', 'A000000677010111').$account;
        $payload = $this->tlv('00', '01')
            .$this->tlv('01', '12')
            .$this->tlv('29', $merchant)
            .$this->tlv('58', 'TH')
            .$this->tlv('53', '764')
            .$this->tlv('54', number_format($amount, 2, '.', ''))
            .'6304';

        return $payload.$this->crc16($payload);
    }

    private function tlv(string $id, string $value): string
    {
        return $id.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private function crc16(string $value): string
    {
        $crc = 0xFFFF;
        foreach (unpack('C*', $value) as $byte) {
            $crc ^= $byte << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
