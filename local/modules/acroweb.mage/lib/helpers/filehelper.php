<?php

namespace Acroweb\Mage\Helpers;

use Acroweb\Mage\CacheTrait;
use Bitrix\Main\FileTable;
use Bitrix\Main\Type\Collection;
use CFile;
use Throwable;

class FileHelper
{
    use CacheTrait;

    /**
     * Retrieves file information for one or multiple file IDs.
     *
     * This method fetches file data from the cache or database, normalizes the input,
     * and returns an array of file information including the file source URL.
     *
     * @param int|array $ids Single file ID as an integer or an array of file IDs
     * @return array An associative array where keys are file IDs and values are file information arrays.
     *               Each file information array includes 'SRC' key with the file source URL.
     *               Returns an empty array if no valid files are found.
     */
    public static function getFilesByIds(int|array $ids): array
    {
        $arFileIds = is_array($ids) ? $ids : [$ids];
        Collection::normalizeArrayValuesByInt($arFileIds, false);

        $result = $request = [];

        if (empty($arFileIds)) {
            return $result;
        }

        foreach ($arFileIds as $fileId) {
            $file = self::getCache($fileId);
            if (is_null($file)) {
                $request[] = $fileId;
            }
        }

        if (empty($request)) {
            return $result;
        }

        try {
            $dbFiles = FileTable::getList(['filter' => ['=ID' => $request]]);
            while ($arFile = $dbFiles->fetch()) {
                self::setCache((int)$arFile['ID'], $arFile);
            }
        } catch (Throwable) {
        }

        foreach ($arFileIds as $fileId) {
            $arFile = self::getCache((int)$fileId);

            if (is_array($arFile)) {
                $arFile['SRC'] = CFile::GetFileSRC($arFile);
                $result[$arFile['ID']] = $arFile;
            }
        }

        return $result;
    }

    /**
     * Get file information including size, format, and URL.
     *
     * @param int $fileId The ID of the file
     * @return array File information
     */
    public static function getFilePath(int $fileId): array|false
    {
        $fileInfo = [
            'size' => '',
            'format' => '',
            'url' => '',
        ];

        $fileArray = CFile::GetFileArray($fileId);
        if (!$fileArray) {
            return false;
        }

        $fileInfo['size'] = CFile::FormatSize($fileArray['FILE_SIZE']);
        $fileInfo['format'] = strtoupper(pathinfo($fileArray['FILE_NAME'], PATHINFO_EXTENSION));
        $fileInfo['url'] = CFile::GetPath($fileId);

        return $fileInfo;
    }
}