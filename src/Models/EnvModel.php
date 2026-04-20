<?php
namespace App\Models;

class EnvModel {
    private static function getEnvPath() {
        return dirname(__DIR__, 2) . '/.env';
    }

    public static function getEnvVariables() {
        $path = self::getEnvPath();
        if (!file_exists($path)) {
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $vars = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // 앞뒤 따옴표 제거
                if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                    // 이스케이프된 따옴표 복원
                    $value = str_replace(['\\\\', '\"', "\\'"], ['\\', '"', "'"], $value);
                }
                
                $vars[$key] = $value;
            }
        }
        
        return $vars;
    }

    public static function updateEnvVariable($key, $value) {
        $path = self::getEnvPath();
        
        // 공백 문자 등이 포함되어 있으면 큰따옴표로 감싸기
        $safeValue = $value;
        if (preg_match('/[= \'"$\\\\]/', $value) || empty($value)) {
            $safeValue = '"' . str_replace(['\\', '"'], ['\\\\', '\"'], $value) . '"';
        }

        if (!file_exists($path)) {
            file_put_contents($path, '');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) $lines = [];
        
        $newLines = [];
        $found = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed) || strpos($trimmed, '#') === 0) {
                $newLines[] = $line;
                continue;
            }
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $k = trim($parts[0]);
                if ($k === $key) {
                    $newLines[] = "{$key}={$safeValue}";
                    $found = true;
                } else {
                    $newLines[] = $line;
                }
            } else {
                $newLines[] = $line;
            }
        }

        if (!$found) {
            $newLines[] = "{$key}={$safeValue}";
        }

        return file_put_contents($path, implode(PHP_EOL, $newLines) . PHP_EOL) !== false;
    }

    public static function deleteEnvVariable($key) {
        $path = self::getEnvPath();
        if (!file_exists($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) return false;
        
        $newLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed) || strpos($trimmed, '#') === 0) {
                $newLines[] = $line;
                continue;
            }
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $k = trim($parts[0]);
                if ($k === $key) {
                    continue; // 삭제될 라인 스킵
                }
            }
            $newLines[] = $line;
        }

        return file_put_contents($path, implode(PHP_EOL, $newLines) . PHP_EOL) !== false;
    }
}