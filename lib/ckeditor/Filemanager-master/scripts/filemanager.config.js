{
		"_comment": "IMPORTANT : go to the wiki page to know about options configuration https://github.com/simogeo/Filemanager/wiki/Filemanager-configuration-file",
    "options": {
        "culture": "en",
        "lang": "php",
        "defaultViewMode": "grid",
        "autoload": true,
        "showFullPath": false,
        "browseOnly": false,
        "showConfirmation": true,
        "showThumbs": true,
        "searchBox": true,
        "listFiles": true,
        "fileSorting": "default",
        "chars_only_latin": true,
        "dateFormat": "d M Y H:i",
        "serverRoot": true,
        "fileRoot": "/",
        "relPath": "/",
        "logger": false,
        "plugins": []
    },
    "security": {
        "uploadPolicy": "DISALLOW_ALL",
        "uploadRestrictions": [
            "jpg",
            "jpeg",
            "gif",
            "png",
            "svg",
            "txt",
            "pdf",
            "odp",
            "ods",
            "odt",
            "rtf",
            "doc",
            "docx",
            "xls",
            "xlsx",
            "ppt",
            "pptx",
            "ogv",
            "mp4",
            "webm",
            "ogg",
            "mp3",
            "wav"
        ]
    },
    "upload": {
        "overwrite": false,
        "imagesOnly": false,
        "fileSizeLimit": 20
    },
    "exclude": {
        "unallowed_files": [
            ".htaccess",
			"__htaccess",
			"favicon.ico",
			"robots.txt",
			"robots.php",
			"node.php",
			"sitemap.php",
			"*.bak"
        ],
        "unallowed_dirs": [
            "_thumbs",
            ".CDN_ACCESS_LOGS",
            "cloudservers", 
			"CVS",
			"tmp",
			"backoffice",
			"frontoffice",
			"include",
			"modules",
			"gabarit",
			"css",
			"js",
			"lib",
			"newsletter",
			"search"
        ],
        "unallowed_files_REGEXP": "/^\\./uis",
        "unallowed_dirs_REGEXP": "/^\\./uis"
    },
    "images": {
        "imagesExt": [
            "jpg",
            "jpeg",
            "gif",
            "png",
            "svg"
        ]
    },
    "videos": {
        "showVideoPlayer": true,
        "videosExt": [
            "ogv",
            "mp4",
            "webm"
        ],
        "videosPlayerWidth": 400,
        "videosPlayerHeight": 222
    },
    "audios": {
        "showAudioPlayer": true,
        "audiosExt": [
            "ogg",
            "mp3",
            "wav"
        ]
    },
    "extras": {
        "extra_js": [],
        "extra_js_async": true
    },
    "icons": {
        "path": "images/fileicons/",
        "directory": "_Open.png",
        "default": "default.png"
    }
}