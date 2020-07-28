<?php

namespace TheLion\UseyourDrive;

abstract class EntryAbstract
{
    public $id;
    public $name;
    public $basename;
    public $path;
    public $parents;
    public $extension;
    public $mimetype;
    public $trashed;
    public $is_dir = false;
    public $size;
    public $description;
    public $last_edited;
    public $last_edited_str;
    public $preview_link;
    public $download_link;
    public $direct_download_link;
    public $export_links;
    public $save_as = [];
    public $can_preview_by_cloud = false;
    public $can_edit_by_cloud = false;
    public $permissions = [
        'canpreview' => false,
        'candelete' => false,
        'canadd' => false,
        'canrename' => false,
        'canmove' => false,
    ];
    public $has_own_thumbnail = false;
    public $thumbnail_icon = false;
    public $thumbnail_small = false;
    public $thumbnail_small_cropped = false;
    public $thumbnail_large = false;
    public $thumbnail_original;
    public $folder_thumbnails = [];
    public $icon;
    public $backup_icon;
    public $media;
    public $shortcut_details = [];
    public $additional_data = [];
    // Parent folder, only used for displaying the Previous Folder entry
    public $pf = false;

    /**
     * Folders that only have a structural function and cannot be used to perform any actions (e.g. delete/rename/zip)
     * My Drive and Team Folders are such folders.
     */
    public $_special_folder = false;

    public function __construct($api_entry = null)
    {
        if (null !== $api_entry) {
            $this->convert_api_entry($api_entry);
        }

        $this->backup_icon = $this->get_default_icon();
    }

    public function __toString()
    {
        return serialize($this);
    }

    abstract public function convert_api_entry($entry);

    public function to_array()
    {
        $entry = (array) $this;

        // Remove Unused data
        //unset($entry['id']);
        unset($entry['parents'], $entry['mimetype'], $entry['direct_download_link'], $entry['additional_data']);

        $entry['size'] = ($entry['size'] > 0) ? $entry['size'] : '';

        return $entry;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        return $this->id = $id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        return $this->name = $name;
    }

    public function get_basename()
    {
        return $this->basename;
    }

    public function set_basename($basename)
    {
        return $this->basename = $basename;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function set_path($path)
    {
        return $this->path = $path;
    }

    public function get_parents()
    {
        return $this->parents;
    }

    public function set_parents($parents)
    {
        return $this->parents = $parents;
    }

    public function has_parents()
    {
        return !empty($this->parents);
    }

    public function get_extension()
    {
        return $this->extension;
    }

    public function set_extension($extension)
    {
        return $this->extension = $extension;
    }

    public function get_mimetype()
    {
        return $this->mimetype;
    }

    public function set_mimetype($mimetype)
    {
        return $this->mimetype = $mimetype;
    }

    public function get_is_dir()
    {
        return $this->is_dir;
    }

    public function is_dir()
    {
        return $this->is_dir;
    }

    public function is_file()
    {
        return !$this->is_dir;
    }

    public function set_is_dir($is_dir)
    {
        return $this->is_dir = (bool) $is_dir;
    }

    public function get_size()
    {
        return $this->size;
    }

    public function set_size($size)
    {
        return $this->size = (int) $size;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_description($description)
    {
        return $this->description = $description;
    }

    public function get_created_time()
    {
        return $this->created_time;
    }

    public function get_created_time_str()
    {
        // Add datetime string for browser that doen't support toLocaleDateString
        $created_time = $this->get_created_time();
        if (empty($created_time)) {
            return '';
        }

        $localtime = get_date_from_gmt(date('Y-m-d H:i:s', strtotime($created_time)));
        $this->created_time_str = date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($localtime));

        return $this->created_time_str;
    }

    public function set_created_time($created_time)
    {
        return $this->created_time = $created_time;
    }

    public function get_last_edited()
    {
        return $this->last_edited;
    }

    public function get_last_edited_str()
    {
        // Add datetime string for browser that doen't support toLocaleDateString
        $last_edited = $this->get_last_edited();
        if (empty($last_edited)) {
            return '';
        }

        $localtime = get_date_from_gmt(date('Y-m-d H:i:s', strtotime($last_edited)));
        $this->last_edited_str = date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($localtime));

        return $this->last_edited_str;
    }

    public function set_last_edited($last_edited)
    {
        return $this->last_edited = $last_edited;
    }

    public function get_preview_link()
    {
        return $this->preview_link;
    }

    public function set_preview_link($preview_link)
    {
        return $this->preview_link = $preview_link;
    }

    public function get_download_link()
    {
        return $this->download_link;
    }

    public function set_download_link($download_link)
    {
        return $this->download_link = $download_link;
    }

    public function get_direct_download_link()
    {
        return $this->direct_download_link;
    }

    public function set_direct_download_link($direct_download_link)
    {
        return $this->direct_download_link = $direct_download_link;
    }

    public function get_export_link($mimetype)
    {
        if (!isset($this->export_links[$mimetype])) {
            return null;
        }

        return $this->export_links[$mimetype];
    }

    public function get_export_links()
    {
        return $this->export_links;
    }

    public function set_export_links($export_links)
    {
        return $this->export_links = $export_links;
    }

    public function get_save_as()
    {
        return $this->save_as;
    }

    public function set_save_as($save_as)
    {
        return $this->save_as = $save_as;
    }

    public function get_can_preview_by_cloud()
    {
        return $this->can_preview_by_cloud;
    }

    public function set_can_preview_by_cloud($can_preview_by_cloud)
    {
        return $this->can_preview_by_cloud = $can_preview_by_cloud;
    }

    public function get_can_edit_by_cloud()
    {
        return $this->can_edit_by_cloud;
    }

    public function set_can_edit_by_cloud($can_edit_by_cloud)
    {
        return $this->can_edit_by_cloud = $can_edit_by_cloud;
    }

    public function get_permission($permission)
    {
        if (!isset($this->permissions[$permission])) {
            return null;
        }

        return $this->permissions[$permission];
    }

    public function get_permissions()
    {
        return $this->permissions;
    }

    public function set_permissions($permissions)
    {
        return $this->permissions = $permissions;
    }

    public function set_permissions_by_key($key, $permissions)
    {
        return $this->permissions[$key] = $permissions;
    }

    public function has_own_thumbnail()
    {
        return $this->has_own_thumbnail;
    }

    public function set_has_own_thumbnail($v)
    {
        return $this->has_own_thumbnail = (bool) $v;
    }

    public function get_trashed()
    {
        return $this->trashed;
    }

    public function set_trashed($v)
    {
        return $this->trashed = (bool) $v;
    }

    public function get_thumbnail_icon()
    {
        return $this->thumbnail_icon;
    }

    public function set_thumbnail_icon($thumbnail_icon)
    {
        return $this->thumbnail_icon = $thumbnail_icon;
    }

    public function get_thumbnail_small()
    {
        return $this->thumbnail_small;
    }

    public function set_thumbnail_small($thumbnail_small)
    {
        return $this->thumbnail_small = $thumbnail_small;
    }

    public function get_thumbnail_small_cropped()
    {
        return $this->thumbnail_small_cropped;
    }

    public function set_thumbnail_small_cropped($thumbnail_small_cropped)
    {
        return $this->thumbnail_small_cropped = $thumbnail_small_cropped;
    }

    public function get_thumbnail_large()
    {
        return $this->thumbnail_large;
    }

    public function set_thumbnail_large($thumbnail_large)
    {
        return $this->thumbnail_large = $thumbnail_large;
    }

    public function get_thumbnail_original()
    {
        return $this->thumbnail_original;
    }

    public function set_thumbnail_original($thumbnail_original)
    {
        return $this->thumbnail_original = $thumbnail_original;
    }

    public function set_folder_thumbnails($folder_thumbnails)
    {
        return $this->folder_thumbnails = $folder_thumbnails;
    }

    public function get_folder_thumbnails()
    {
        return $this->folder_thumbnails;
    }

    public function get_icon()
    {
        if (empty($this->icon)) {
            return $this->get_default_thumbnail_icon();
        }

        return $this->icon;
    }

    public function set_icon($icon)
    {
        return $this->icon = $icon;
    }

    public function get_media($setting = null)
    {
        if (!empty($setting)) {
            if (isset($this->media[$setting])) {
                return $this->media[$setting];
            }

            return null;
        }

        return $this->media;
    }

    public function set_media($media)
    {
        return $this->media = $media;
    }

    public function get_additional_data()
    {
        return $this->additional_data;
    }

    public function set_additional_data($additional_data)
    {
        return $this->additional_data = $additional_data;
    }

    public function is_parent_folder()
    {
        return $this->pf;
    }

    public function set_parent_folder($value)
    {
        return $this->pf = (bool) $value;
    }

    public function get_default_icon()
    {
        return $this->get_default_thumbnail_icon();
    }

    public function get_default_thumbnail_icon()
    {
        return Helpers::get_default_thumbnail_icon($this->get_mimetype());
    }

    public function is_special_folder()
    {
        return false !== $this->_special_folder;
    }

    public function get_special_folder()
    {
        return $this->_special_folder;
    }

    public function set_special_folder($value)
    {
        $this->_special_folder = $value;
    }

    public function get_shortcut_details($key = null)
    {
        if (!empty($key)) {
            if (isset($this->shortcut_details[$key])) {
                return $this->shortcut_details[$key];
            }

            return null;
        }

        return $this->shortcut_details;
    }

    public function set_shortcut_details($shortcut_details)
    {
        return $this->shortcut_details = $shortcut_details;
    }

    public function is_shortcut()
    {
        return !empty($this->shortcut_details);
    }
}

class Entry extends EntryAbstract
{
    public function convert_api_entry($api_entry)
    {
        if (
                !$api_entry instanceof \UYDGoogle_Service_Drive_DriveFile
        ) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Google response is not a valid Entry.'));
            die();
        }

        // @var $api_entry \UYDGoogle_Service_Drive_DriveFile

        // Normal Meta Data
        $this->set_id($api_entry->getId());
        $this->set_name($api_entry->getName());
        $this->set_extension(strtolower($api_entry->getFileExtension()));

        // Shortcuts don't provide extension information
        if ('application/vnd.google-apps.shortcut' === $api_entry->getMimeType()) {
            $pathinfo = Helpers::get_pathinfo($this->get_name());
            if (isset($pathinfo['extension'])) {
                $this->set_extension($pathinfo['extension']);
            }
        }

        $this->set_mimetype($api_entry->getMimeType());

        if (empty($this->extension)) {
            $this->set_basename($this->name);
        } else {
            $this->set_basename(str_replace('.'.$this->get_extension(), '', $api_entry->getName()));
        }

        $this->set_trashed($api_entry->getTrashed());
        $this->set_is_dir(('application/vnd.google-apps.folder' === $api_entry->getMimeType()));
        $this->set_size(($this->get_is_dir()) ? 0 : $api_entry->getSize());
        $this->set_description($api_entry->getDescription());
        $this->set_last_edited($api_entry->getModifiedTime());
        $this->set_created_time($api_entry->getCreatedTime());

        // Set Parents
        $this->set_parents($api_entry->getParents());

        // Set Shortcut
        $shortcut_details = $api_entry->getShortcutDetails();
        if (!empty($shortcut_details)) {
            $this->set_shortcut_details(
                [
                    'targetId' => $shortcut_details->getTargetId(),
                    'targetMimeType' => $shortcut_details->getTargetMimeType(), ]
            );
        }

        // Download & Export links
        $this->set_direct_download_link($api_entry->getWebContentLink());
        $this->set_save_as($this->create_save_as());
        $this->set_export_links($api_entry->getExportLinks());

        // Can file be viewed be previewed be google
        $preview_link = $api_entry->getWebViewLink();
        if (!empty($preview_link) && (!in_array($this->get_extension(), ['zip']) && $this->is_file())) {
            $this->set_can_preview_by_cloud(true);
        }
        $this->set_preview_link($preview_link);

        // Set Permission
        $capabilities = $api_entry->getCapabilities();

        $canpreview = false;
        $candownload = false;
        $canshare = false;
        $candelete = $cantrash = $api_entry->getOwnedByMe();
        $canadd = $api_entry->getOwnedByMe();
        $canmove = $api_entry->getOwnedByMe();
        $canrename = $api_entry->getOwnedByMe();
        $canchangecopyrequireswriterpermission = true;

        if (!empty($capabilities)) {
            $this->set_can_edit_by_cloud($capabilities->getCanEdit() && $this->edit_supported_by_cloud());
            $canadd = $capabilities->getCanEdit();
            $canrename = $capabilities->getCanRename();
            $canshare = $capabilities->getCanShare();
            $candelete = $capabilities->getCanDelete();
            $cantrash = $capabilities->getCanTrash();
            $canmove = $capabilities->getCanCopy();
            $canchangecopyrequireswriterpermission = $capabilities->getCanChangeCopyRequiresWriterPermission();
        }

        // Download permissions are a little bit tricky
        $users = [];
        $api_permissions = $api_entry->getPermissions();
        if (count($api_permissions) > 0) {
            foreach ($api_permissions as $permission) {
                $users[$permission->getId()] = ['type' => $permission->getType(), 'role' => $permission->getRole(), 'domain' => $permission->getDomain()];
            }
        }

        $candownload = true;
        $canpreview = true;

        // Set the permissions
        $permissions = [
            'canpreview' => $canpreview,
            'candownload' => $candownload,
            'candelete' => $candelete,
            'cantrash' => $cantrash,
            'canmove' => $canmove,
            'canadd' => $canadd,
            'canrename' => $canrename,
            'canshare' => $canshare,
            'copyRequiresWriterPermission' => $api_entry->getCopyRequiresWriterPermission(),
            'canChangeCopyRequiresWriterPermission' => $canchangecopyrequireswriterpermission,
            'users' => $users,
        ];

        $this->set_permissions($permissions);

        // Icon
        $icon = $api_entry->getIconLink();
        $this->set_icon(str_replace(['/16/', '+shared'], ['/64/', ''], $icon));

        // Thumbnail
        $this->set_thumbnails($api_entry->getThumbnailLink());

        // If entry has media data available set it here
        $mediadata = [];
        $imagemetadata = $api_entry->getImageMediaMetadata();
        $videometadata = $api_entry->getVideoMediaMetadata();
        if (!empty($imagemetadata)) {
            if (empty($imagemetadata->rotation) || 0 === $imagemetadata->getRotation()|| 2 === $imagemetadata->getRotation()) {
                $mediadata['width'] = $imagemetadata->getWidth();
                $mediadata['height'] = $imagemetadata->getHeight();
            } else {
                $mediadata['width'] = $imagemetadata->getHeight();
                $mediadata['height'] = $imagemetadata->getWidth();
            }

            if (!empty($imagemetadata->time)) {
                $dtime = \DateTime::createFromFormat('Y:m:d H:i:s', $imagemetadata->getTime(), new \DateTimeZone('UTC'));

                if ($dtime) {
                    $mediadata['time'] = $dtime->getTimestamp();
                }
            }
        } elseif (!empty($videometadata)) {
            $mediadata['width'] = $videometadata->getWidth();
            $mediadata['height'] = $videometadata->getHeight();
            $mediadata['duration'] = $videometadata->getDurationMillis();
        }

        $this->set_media($mediadata);

        // Add some data specific for Google Drive Service
        $additional_data = [
            //'can_viewers_copy_content' => $api_entry->getViewersCanCopyContent()
        ];

        $this->set_additional_data($additional_data);
    }

    public function set_thumbnails($thumbnail)
    {
        $icon = $this->get_icon();

        if (empty($thumbnail)) {
            $this->set_thumbnail_icon($icon);
            $this->set_thumbnail_small(str_replace('/64/', '/256/', $icon));
            $this->set_thumbnail_small_cropped(str_replace('/64/', '/256/', $icon));
        } elseif (false !== strpos($thumbnail, 'google.com')) {
            // Thumbnails with feeds in URL give 404 without token?
            $thumbnail_small = USEYOURDRIVE_ADMIN_URL.'?action=useyourdrive-thumbnail&s=small&id='.$this->get_id();
            $thumbnail_cropped = USEYOURDRIVE_ADMIN_URL.'?action=useyourdrive-thumbnail&s=cropped&id='.$this->get_id();
            $thumbnail_large = USEYOURDRIVE_ADMIN_URL.'?action=useyourdrive-thumbnail&s=large&c=0&id='.$this->get_id();
            $this->set_has_own_thumbnail(true);
            $this->set_thumbnail_icon($icon);
            $this->set_thumbnail_small($thumbnail_small);
            $this->set_thumbnail_small_cropped($thumbnail_cropped);
            $this->set_thumbnail_large($thumbnail_large);
            $this->set_thumbnail_original($thumbnail);
        } else {
            $this->set_has_own_thumbnail(true);
            $this->set_thumbnail_icon(str_replace('=s220', '=s16-c-nu', $thumbnail));
            $this->set_thumbnail_small(str_replace('=s220', '=w500-h375-nu', $thumbnail));
            $this->set_thumbnail_small_cropped(str_replace('=s220', '=w500-h375-c-nu', $thumbnail));
            $this->set_thumbnail_large(str_replace('=s220', '', $thumbnail));
            $this->set_thumbnail_original($thumbnail);
        }
    }

    public function get_thumbnail_with_size($thumbnailsize, $thumbnail_url = null)
    {
        if (empty($thumbnail_url)) {
            $thumbnail_url = $this->get_thumbnail_small();
        }

        return str_replace('=w500-h375-nu', '='.$thumbnailsize, $thumbnail_url);
    }

    public function create_save_as()
    {
        switch ($this->get_mimetype()) {
            case 'application/vnd.google-apps.document':
                $save_as = [
                    'MS Word document' => ['mimetype' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'extension' => 'docx', 'icon' => 'fa-file-word'], // First is default
                    'HTML' => ['mimetype' => 'text/html', 'extension' => 'html', 'icon' => 'fa-file-code'],
                    'Text' => ['mimetype' => 'text/plain', 'extension' => 'txt', 'icon' => 'fa-file-alt'],
                    'Open Office document' => ['mimetype' => 'application/vnd.oasis.opendocument.text', 'extension' => 'odt', 'icon' => 'fa-file-alt'],
                    'PDF' => ['mimetype' => 'application/pdf', 'extension' => 'pdf', 'icon' => 'fa-file-pdf'],
                    'ZIP' => ['mimetype' => 'application/zip', 'extension' => 'zip', 'icon' => 'fa-file-archive'],
                ];

                break;
            case 'application/vnd.google-apps.spreadsheet':
                $save_as = [
                    'MS Excel document' => ['mimetype' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'extension' => 'docx', 'icon' => 'fa-file-excel'],
                    'Open Office sheet' => ['mimetype' => 'application/x-vnd.oasis.opendocument.spreadsheet', 'extension' => 'ods', 'icon' => 'fa-file'],
                    'PDF' => ['mimetype' => 'application/pdf', 'extension' => 'pdf', 'icon' => 'fa-file-pdf'],
                    'CSV (first sheet only)' => ['mimetype' => 'text/csv', 'extension' => 'csv', 'icon' => 'fa-file-alt'],
                    'ZIP' => ['mimetype' => 'application/zip', 'extension' => 'zip', 'icon' => 'fa-file-archive'],
                ];

                break;
            case 'application/vnd.google-apps.drawing':
                $save_as = [
                    'JPEG' => ['mimetype' => 'image/jpeg', 'extension' => 'jpeg', 'icon' => 'fa-file-image'],
                    'PNG' => ['mimetype' => 'image/png', 'extension' => 'png', 'icon' => 'fa-file-image'],
                    'SVG' => ['mimetype' => 'image/svg+xml', 'extension' => 'svg', 'icon' => 'fa-file-code'],
                    'PDF' => ['mimetype' => 'application/pdf', 'extension' => 'pdf', 'icon' => 'fa-file-pdf'],
                ];

                break;
            case 'application/vnd.google-apps.presentation':
                $save_as = [
                    'MS PowerPoint document' => ['mimetype' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'extension' => 'pptx', 'icon' => 'fa-file-powerpoint'],
                    'PDF' => ['mimetype' => 'application/pdf', 'extension' => 'pdf', 'icon' => 'fa-file-pdf'],
                    'Text' => ['mimetype' => 'text/plain', 'extension' => 'txt', 'icon' => 'fa-file-alt'],
                ];

                break;
            case 'application/vnd.google-apps.script':
                $save_as = [
                    'JSON' => ['mimetype' => 'application/vnd.google-apps.script+json', 'extension' => 'json', 'icon' => 'fa-file-code'],
                ];

                break;
            case 'application/vnd.google-apps.form':
                $save_as = [
                    'ZIP' => ['mimetype' => 'application/zip', 'extension' => 'zip', 'icon' => 'fa-file-archive'],
                ];

                break;
            default:
                return [];
        }

        return $save_as;
    }

    public function edit_supported_by_cloud()
    {
        $is_supported = false;

        switch ($this->get_mimetype()) {
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.google-apps.document':
            case'application/vnd.ms-excel':
            case 'application/vnd.ms-excel.sheet.macroenabled.12':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.google-apps.spreadsheet':

            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
            case 'application/vnd.google-apps.presentation':
            case 'application/vnd.google-apps.drawing':
                $is_supported = true;

                break;
            default:
                $is_supported = false;

                break;
        }

        return $is_supported;
    }
}

class DriveEntry extends EntryAbstract
{
    public function convert_api_entry($api_entry)
    {
        if (
                !$api_entry instanceof \UYDGoogle_Service_Drive_TeamDrive
        ) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Google response is not a valid Entry.'));
            die();
        }

        // @var $api_entry \UYDGoogle_Service_Drive_DriveFile

        // Normal Meta Data
        $this->set_id($api_entry->getId());
        $this->set_name($api_entry->getName());
        $this->set_basename($api_entry->getName());
        $this->set_is_dir(true);
        $this->set_size(($this->get_is_dir()) ? 0 : $api_entry->getSize());

        // Set Permission
        $capabilities = $api_entry->getCapabilities();

        $canpreview = false;
        $candownload = false;
        $candelete = $cantrash = false;
        $canadd = false;
        $canrename = false;

        if (!empty($capabilities)) {
            $this->set_can_edit_by_cloud(false);
            $canadd = $capabilities->getCanEdit();
            $canrename = $capabilities->getCanEdit();
        }

        // Set the permissions
        $permissions = [
            'canpreview' => $canpreview,
            'candownload' => $candownload,
            'candelete' => $candelete,
            'cantrash' => $cantrash,
            'canadd' => $canadd,
            'canrename' => $canrename,
        ];

        $this->set_permissions($permissions);

        // Thumbnail
        $this->set_thumbnails($api_entry->getBackgroundImageLink());
    }

    public function set_thumbnails($thumbnail)
    {
        $this->set_has_own_thumbnail(true);

        if (false === strpos($thumbnail, '=')) {
            $thumbnail = $thumbnail.'=w1920-h216-n';
        }

        $this->set_thumbnail_icon(str_replace('=w1920-h216-n', '=s16-c-nu', $thumbnail));
        $this->set_thumbnail_small(str_replace('=w1920-h216-n', '=w500-h375-c-nu', $thumbnail));
        $this->set_thumbnail_small_cropped(str_replace('=w1920-h216-n', '=w500-h375-c-nu', $thumbnail));
        $this->set_thumbnail_large(str_replace('=w1920-h216-n', '', $thumbnail));
        $this->set_thumbnail_original($thumbnail);
    }

    public function get_thumbnail_with_size($thumbnailsize)
    {
        if (false !== strpos($this->get_thumbnail_small(), 'use-your-drive-cache')) {
            return $this->get_thumbnail_small();
        }

        return str_replace('=w500-h375-nu', '='.$thumbnailsize, $this->get_thumbnail_small());
    }
}
