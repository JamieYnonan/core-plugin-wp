<?php
namespace CorePluginWp;

class WpImage
{
    private $mimesExtensions = [
        'image/jpeg' => ['jpg', 'jpeg', 'jpe'],
        'image/gif' => ['gif'],
        'image/png' => ['png'],
        'image/bmp' => ['bmp'],
        'image/tiff' => ['tif', 'tiff'],
        'image/x-icon' => ['ico']
    ];
    private $extensions = [];
    private $mimeValidate;
    private $img;
    protected $isUrl;
    protected $mime;
    protected $extension;
    protected $originBaseName;
    protected $originPathName;
    protected $size;
    protected $width;
    protected $heigth;
    protected $name;
    protected $uploadDir;
    protected $imgContent;
    protected $onlyName;
    protected $fullPath;
    protected $attachmentId;
    protected $attachmentMetadata;
    protected $postId;
    protected $postMetaId;
    protected $isThumnail = false;

    public function __construnct($image, $validateMime = null)
    {
        $this->img = new SplFileInfo($image);
        if (!isset(parse_url($image)['scheme'])) {
            if ($this->img->isFile() === false) {
                throw new Exception('image invalid');
            }
            $this->isUrl = false;
        } else {
            $this->isUrl = true;
        }

        $this->validateImage($validateMime);
    }

    /**
     * @return isUrl
     */
    public function getIsUrl()
    {
        return $this->isUrl;
    }

    /**
     * @return mime
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return originBaseName
     */
    public function getOriginBaseName()
    {
        return $this->originBaseName;
    }

    /**
     * @return originPathName
     */
    public function getOriginPathName()
    {
        return $this->originPathName;
    }

    /**
     * @return size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return heigth
     */
    public function getHeigth()
    {
        return $this->heigth;
    }

    /**
     * @return name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return uploadDir
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * @return imgContent
     */
    public function getImgContent()
    {
        return $this->imgContent;
    }

    /**
     * @return onlyName
     */
    public function getOnlyName()
    {
        return $this->onlyName;
    }

    /**
     * @return fullPath
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * @return attachmentId
     */
    public function getAttachmentId()
    {
        return $this->attachmentId;
    }

    /**
     * @return attachmentMetadata
     */
    public function getAttachmentMetadata()
    {
        return $this->attachmentMetadata;
    }

    /**
     * @return postId
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @return postMetaId
     */
    public function getPostMetaId()
    {
        return $this->postMetaId;
    }

    /**
     * @return isThumnail
     */
    public function getIsThumnail()
    {
        return $this->isThumnail;
    }

    private function validateImage($validateMime)
    {
        $this->setMimeValidate($validateMime);
        $this->validateExtension();
        $this->validateMimeType();
    }

    private function setMimeValidate($mime)
    {
        if ($mime !== null) {
            if (!in_array($mime, $this->mimes)) {
                throw new Exception('Mime image invalid');
            }
            $this->mimeValidate = [$mime];
        } else {
             $this->mimeValidate = array_keys($mimesExtensions);
        }
    }

    private function setExtensions()
    {
        if ($this->mimeValidate === null) {
            $extension = array_values($this->mimesExtensions);
            foreach ($extension as $e) {
                 $this->extensions = array_merge($this->extensions, $e);
            }
        } else {
            $this->extension = $this->mimesExtensions[$this->mimeValidate];
        }
    }

    private function validateExtension()
    {
        $this->setExtensions();
        if (!in_array($this->img->getExtension(), $this->extensions)) {
            throw new Exception('invalid extension');
        }
        $this->setSplFileInfoData();
    }

    private function setSplFileInfoData()
    {
        $this->extension = $this->img->getExtension();
        $this->originBaseName = $this->img->getBasename();
        $this->size = $this->img->getSize();
        $this->originPathName = $this->img->getPathname();
    }

    private function validateMimeType()
    {
        $imageSize = getimagesize($this->originPathName);
        if (!in_array($imageSize['mime'])) {
            throw new Exception('mimetype invalid');
        }
        $this->setImageSizeData($imageSize);
    }

    private function setImageSizeData($imageSize)
    {
        $this->width = $imageSize[0];
        $this->heigth = $imageSize[1];
        $this->mime = $imageSize['mime'];
    }

    public function setUploadDir($pathUploadDir = null)
    {
        if ($pathUploadDir === null) {
            $this->uploadDir = wp_upload_dir()['path'];
        } else {
            $path = realpath($path);
            if ($path === false) {
                throw new Exception('Invalid path upload dir');
            }
            $this->uploadDir = $path;
        }
    }

    private function createDir()
    {
         return wp_mkdir_p($this->uploadDir);
    }

    private function setImgContent()
    {
        $this->imgContent = file_get_contents($this->originPathName);
    }

    public function setName($imgName = null)
    {
        if ($imgName === null ) {
            $imgName = str_replace('.'. $this->extension, '', $this->baseName);
        }
        $this->onlyName = sanitize_file_name($imgName);
        $this->name = $this->onlyName . '.'. $this->extension;
    }

    public function save()
    {
        if ($this->uploadDir === null) {
            $this->setUploadDir(null);
        }
        $this->createDir();
        $this->setImgContent();
        if ($this->name === null) {
            $this->setName();
        }
        $this->fullPath = $this->uploadDir . DIRECTORY_SEPARATOR . $this->name;
        file_put_contents($this->fullPath, $this->imgContent);
    }

    private function setPostId()
    {
        $postId = (int)$postId;
        if (!$postId > 0) {
            throw new Exception('postId invalid');
        }
        $this->postId = $postId;
    }

    private function setAttachmentMetadata()
    {
        $this->attachmentMetadata = wp_generate_attachment_metadata(
            $this->attachmentId,
            $this->fullPath
        );
    }

    private function updateAttachmentMetadata()
    {
        wp_update_attachment_metadata($this->attachmentId, $this->attachmentMetadata);
    }

    private function setPostThumbnail()
    {
        $thumb = set_post_thumbnail($this->postId, $this->attahcmentId);
        if ($thumb != false) {
            $this->isThumbnail = true;
        }
    }

    public function insertAttachment($postId, $thumbnail = false, $postTitle = null)
    {
        $this->setPostId($postId);
        $title = ($postTitle === null) ? $this->onlyName : $postTitle;
        $attachment = [
            'post_mime_type' => $this->mime,
            'post_tile' => sanitize_title(),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        $this->attachmentId = wp_insert_attachment(
            $attachment,
            $this->fullPath,
            $this->postId
        );
        if ($this->attachmentId == 0) {
            throw new Exception('error insert attachment');
        }
        $this->setAttachmentMetadata();
        if ($thumbnail === true) {
            $this->setPostThumbnail();
        }
    }
}
