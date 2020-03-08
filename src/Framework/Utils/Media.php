<?php

namespace OP\Framework\Utils;

use Exception;

require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Medias management class
 */
class Media
{
    protected static $download_path;


    public function __construct()
    {
        $path = wp_upload_dir()['path'] ?? '';

        if (!$path) {
            throw new \Exception("wp_upload_dir() did not return required upload dir path.");
        }

        $base_dir = explode('uploads/', $path)[0];

        self::$download_path = $base_dir . 'uploads/downloads';
    }

    /**
     * Download an image to shared directory and return image path
     *
     * @param string $url source url of the image to retrieve
     * @param string $name image name that sould be used
     *
     * @return string path of the creteated image
     */
    public function download($url, $name)
    {
        if (!file_exists(self::$download_path)) {
            if (!mkdir(self::$download_path, 0775, true)) {
                wp_die("Failed to create downloads directory [App\Utils\Media::download].");
            }
        }

        $img_path = self::$download_path . '/' . $name;

        if (!file_put_contents($img_path, file_get_contents($url))) {
            wp_die("Failed to write image to directory [App\Utils\Media::download].");
        }

        return $img_path;
    }


    /**
     * Delete an image from shared directory
     *
     * @param string $name image name that sould be deleted
     *
     * @return bool
     */
    public function delete($name)
    {
        $img_path = self::$download_path . '/' . $name;

        if (file_exists($img_path)) {
            return unlink($img_path);
        }

        return false;
    }


    /**
     * Download an image to shared directory and return image path
     *
     * @param string $name image name
     * @param string $path image path
     * @param int (optionnal) $post_id associated post
     *
     * @return int|null attachement_id
     */
    public function uploadWordpress(string $name, string $path, string $post_id = null)
    {
        $upload_file = wp_upload_bits($name, null, file_get_contents($path));

        if (!$upload_file['error']) {
            $wp_filetype = wp_check_filetype($name, null);

            $attachment = array(
                'post_mime_type'    => $wp_filetype['type'],
                'post_parent'       => $post_id,
                'post_title'        => preg_replace('/\.[^.]+$/', '', $name),
                'post_content'      => '',
                'post_status'       => $post_id != null ? 'inherit' : 'publish'
            );

            $attachment_id = wp_insert_attachment(
                $attachment,
                $upload_file['file'],
                $post_id
            );

            if (!is_wp_error($attachment_id)) {
                $attachment_data = wp_generate_attachment_metadata(
                    $attachment_id,
                    $upload_file['file']
                );

                wp_update_attachment_metadata($attachment_id, $attachment_data);
            }
        } else {
            echo $upload_file['error'];
            error_log($upload_file['error']);
        }

        return $attachment_id ?? null;
    }


    /**
     * Get images from API and upload it to wordpress
     *
     * @param string $url  url of the source image
     * @param string $name Image name to use, use distant image name if not provided
     *
     * @return int|null attachement_id
     */
    public function insertImageFromUrl($url, $name = '', $parent_post_id = null)
    {
        $get_name = explode('/', $url);

        if (empty($name) && is_array($get_name) && !empty($get_name)) {
            $name = $get_name[count($get_name) - 1];
        }

        $path       = $this->download($url, $name);
        $attach_id  = $this->uploadWordpress($name, $path, $parent_post_id);

        $this->delete($name);

        if (!$attach_id) {
            throw new Exception(
                "Image insertion from URL has failed. Please double check writing permissions."
            );
        }

        return $attach_id;
    }
}
