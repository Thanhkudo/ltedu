@php
    $metaTitle = trim($__env->yieldContent('meta_title', $__env->yieldContent('title', 'LTEdu'))) ?: 'LTEdu';
    $metaDescription = trim($__env->yieldContent('meta_description', 'LTEdu là hệ thống quản lý lớp học, giao bài tập, luyện tập trực tuyến và theo dõi kết quả học viên.'));
    $metaKeywords = trim($__env->yieldContent('meta_keywords', 'LTEdu, học tiếng Anh, quản lý lớp học, bài tập trực tuyến, kho câu hỏi, học viên, giáo viên'));
    $metaImage = trim($__env->yieldContent('og_image', asset('images/og-image.png')));
    $metaUrl = trim($__env->yieldContent('og_url', url()->current()));
@endphp
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:title" content="{{ $metaTitle }} - LTEdu">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $metaUrl }}">
