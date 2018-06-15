<?php

namespace App\Service;


class TemplateCreator
{
    private const TEMPLATE_HEADER = '_header.html.twig';
    private const TEMPLATE_ADMIN_LOGIN = '_login.html.twig';
    private const SAVE_DIR_NAME = 'files';

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * TemplateCreator constructor.
     * @param string $projectPath
     * @param string $templatePath
     * @internal param string $projectPath
     */
    public function __construct(string $projectPath, string $templatePath)
    {
        $this->projectPath = $projectPath;
        $this->templatePath = $templatePath;
    }

    /**
     * @param string $containerName
     * @param string $link
     * @return string
     */
    public function overrideHeaderTemplate(string $containerName, string $link): string
    {
        $file_contents = file_get_contents($this->templatePath. DIRECTORY_SEPARATOR .self::TEMPLATE_HEADER);
        $file_contents = str_replace('logo', $link, $file_contents);

        $saveDir = '/application' . DIRECTORY_SEPARATOR. self::SAVE_DIR_NAME. DIRECTORY_SEPARATOR . $containerName;
        if (!is_dir($saveDir)) {
            if (!mkdir($saveDir, 0777, true) && !is_dir($saveDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $saveDir));
            }
        }

        $savePath = $saveDir . DIRECTORY_SEPARATOR . self::TEMPLATE_HEADER;
        file_put_contents($savePath, $file_contents);


        return $this->projectPath. DIRECTORY_SEPARATOR. self::SAVE_DIR_NAME. DIRECTORY_SEPARATOR . $containerName. DIRECTORY_SEPARATOR . self::TEMPLATE_HEADER;
    }

    /**
     * @param string $containerName
     * @param string $link
     * @return string
     */
    public function overrideAdminLoginTemplate(string $containerName, string $link): string
    {
        $file_contents = file_get_contents($this->templatePath. DIRECTORY_SEPARATOR . self::TEMPLATE_ADMIN_LOGIN);
        $file_contents = str_replace('admin_logo', $link, $file_contents);

        $saveDir = '/application' . DIRECTORY_SEPARATOR. self::SAVE_DIR_NAME. DIRECTORY_SEPARATOR . $containerName;
        if (!is_dir($saveDir)) {
            if (!mkdir($saveDir, 0777, true) && !is_dir($saveDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $saveDir));
            }
        }

        $savePath = $saveDir . DIRECTORY_SEPARATOR . self::TEMPLATE_ADMIN_LOGIN;
        file_put_contents($savePath, $file_contents);

        return $this->projectPath. DIRECTORY_SEPARATOR. self::SAVE_DIR_NAME. DIRECTORY_SEPARATOR . $containerName. DIRECTORY_SEPARATOR . self::TEMPLATE_ADMIN_LOGIN;
    }

}
