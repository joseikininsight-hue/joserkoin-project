<?php
/**
 * MINIMALIST STYLISH Header Template - Enhanced Version
 * モダンでスタイリッシュな白黒ヘッダー - 英語統一版
 * 
 * @package Minimalist_Stylish_Header
 * @version 3.0.0-enhanced
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#000000">
    
    <?php wp_head(); ?>
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <?php if (is_front_page() || is_home()) : ?>
    <!-- Preload LCP Hero Image for Homepage -->
    <link rel="preload" as="image" href="https://joseikin-insight.com/wp-content/uploads/2025/10/1.png" fetchpriority="high">
    <?php endif; ?>
    
    <!-- Optimized Font Loading with font-display: swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&family=Outfit:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&family=Outfit:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>
    
    <!-- Critical CSS - Above-the-fold optimization for PageSpeed -->
    <style>
        /* ===============================================
           CRITICAL CSS - Above-the-fold Only
           =============================================== */
        
        /* System font stack for instant text rendering */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #0a0a0a;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Header Critical Styles */
        .stylish-header-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        
        .stylish-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 960px;
            margin: 0 auto;
            padding: 8px 16px;
        }
        
        .stylish-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .stylish-logo-text h1,
        .stylish-logo-text p {
            margin: 0;
            padding: 0;
        }
        
        .stylish-logo-text h1 {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
            line-height: 1.2;
        }
        
        /* Hero Section Critical Styles */
        .gih-hero-section {
            min-height: 100vh;
            padding: 120px 20px 80px;
            background: #ffffff;
        }
        
        .gih-container {
            max-width: 960px;
            margin: 0 auto;
        }
        
        .gih-hero-image,
        .gih-mobile-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Prevent layout shift for images */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Web fonts applied after load with font-display: swap already in URL */
    </style>
    
    <!-- Font Awesome - Async Load -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <style>
        /* ===============================================
           ENHANCED MINIMALIST HEADER STYLES
           =============================================== */
        
        :root {
            /* Color System */
            --color-black: #000000;
            --color-gray-900: #0a0a0a;
            --color-gray-800: #1a1a1a;
            --color-gray-700: #2a2a2a;
            --color-gray-600: #4a4a4a;
            --color-gray-500: #6a6a6a;
            --color-gray-400: #8a8a8a;
            --color-gray-300: #c0c0c0;
            --color-gray-200: #e0e0e0;
            --color-gray-100: #f5f5f5;
            --color-white: #ffffff;
            
            /* Text Colors */
            --text-primary: #0a0a0a;
            --text-secondary: #4a4a4a;
            --text-tertiary: #8a8a8a;
            --text-inverse: #ffffff;
            
            /* Background Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #fafafa;
            --bg-tertiary: #f5f5f5;
            --bg-dark: #0a0a0a;
            --bg-overlay: rgba(255, 255, 255, 0.98);
            --bg-overlay-dark: rgba(10, 10, 10, 0.95);
            
            /* Borders */
            --border-light: rgba(0, 0, 0, 0.06);
            --border-medium: rgba(0, 0, 0, 0.12);
            --border-dark: rgba(0, 0, 0, 0.2);
            
            /* Shadows */
            --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.02);
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 16px 32px rgba(0, 0, 0, 0.12);
            
            /* Spacing */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            
            /* Border Radius */
            --radius-sm: 2px;
            --radius-md: 4px;
            --radius-lg: 6px;
            --radius-xl: 8px;
            --radius-2xl: 12px;
            --radius-full: 9999px;
            
            /* Transitions */
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Typography */
            --font-primary: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-secondary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-weight-light: 300;
            --font-weight-normal: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --font-weight-extrabold: 800;
            
            /* Layout */
            --header-height: 4.5rem;
            --max-width: 960px;
        }
        
        * {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: var(--font-secondary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-weight: var(--font-weight-normal);
        }
        
        /* ===============================================
           ANNOUNCEMENT BAR
           =============================================== */
        .stylish-announcement {
            background: var(--bg-dark);
            color: var(--text-inverse);
            text-align: center;
            padding: var(--space-3) var(--space-4);
            font-size: 0.8125rem;
            font-weight: var(--font-weight-medium);
            letter-spacing: 0.02em;
            position: relative;
            z-index: 1001;
            transform: translateY(0);
            transition: transform var(--transition-base);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stylish-announcement.hidden {
            transform: translateY(-100%);
        }
        
        .stylish-announcement a {
            color: var(--text-inverse);
            text-decoration: none;
            margin-left: var(--space-3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            transition: border-color var(--transition-fast);
            font-weight: var(--font-weight-semibold);
        }
        
        .stylish-announcement a:hover {
            border-bottom-color: var(--text-inverse);
        }
        
        /* ===============================================
           MAIN HEADER
           =============================================== */
        .stylish-header {
            background: var(--bg-overlay);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border-light);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all var(--transition-base);
            transform: translateY(0);
            will-change: transform;
        }
        
        .stylish-header.with-announcement {
            top: 2.75rem;
        }
        
        .stylish-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow-sm);
            top: 0;
        }
        
        .stylish-header.hidden {
            transform: translateY(-100%);
        }
        
        .stylish-container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 var(--space-5);
        }
        
        @media (min-width: 768px) {
            .stylish-container {
                padding: 0 var(--space-6);
            }
        }
        
        .stylish-header-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            height: var(--header-height);
            position: relative;
            gap: var(--space-8);
        }
        
        @media (min-width: 1024px) {
            .stylish-header-inner {
                justify-content: space-between;
            }
        }
        
        /* ===============================================
           LOGO
           =============================================== */
        .stylish-logo {
            display: flex;
            align-items: center;
            gap: var(--space-4);
            text-decoration: none;
            transition: opacity var(--transition-fast);
            flex-shrink: 0;
        }
        
        .stylish-logo:hover {
            opacity: 0.7;
        }
        
        .stylish-logo-image {
            height: 1.75rem;
            width: auto;
            aspect-ratio: 200 / 60;
            object-fit: contain;
            transition: transform var(--transition-fast);
        }
        
        @media (min-width: 768px) {
            .stylish-logo-image {
                height: 2rem;
            }
        }
        
        .stylish-logo-text {
            display: none;
        }
        
        @media (min-width: 640px) {
            .stylish-logo-text {
                display: block;
            }
        }
        
        .stylish-logo-text h1,
        .stylish-logo-text .site-name {
            margin: 0;
            font-size: 1rem;
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            line-height: 1.2;
            letter-spacing: -0.03em;
            font-family: var(--font-primary);
        }
        
        @media (min-width: 768px) {
            .stylish-logo-text h1 {
                font-size: 1.125rem;
            }
        }
        
        .stylish-logo-text p {
            margin: 0;
            font-size: 0.6875rem;
            color: var(--text-tertiary);
            font-weight: var(--font-weight-medium);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        /* ===============================================
           NAVIGATION - ENHANCED WITH ENGLISH + JAPANESE
           =============================================== */
        .stylish-nav {
            display: none;
            align-items: center;
            gap: var(--space-2);
            flex: 1;
            justify-content: center;
            margin: 0 var(--space-8);
        }
        
        @media (min-width: 1024px) {
            .stylish-nav {
                display: flex;
            }
        }
        
        .stylish-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            padding: var(--space-3) var(--space-5);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: var(--font-weight-semibold);
            font-size: 0.9375rem;
            border-radius: var(--radius-lg);
            position: relative;
            transition: all var(--transition-fast);
            white-space: nowrap;
            letter-spacing: 0.02em;
            font-family: var(--font-primary);
        }
        
        .stylish-nav-link:hover {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            transform: translateY(-1px);
        }
        
        .stylish-nav-link-main {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            font-size: 0.9375rem;
            font-weight: var(--font-weight-semibold);
        }
        
        .stylish-nav-link-sub {
            font-size: 0.625rem;
            color: var(--text-tertiary);
            font-weight: var(--font-weight-medium);
            letter-spacing: 0.03em;
            opacity: 0.8;
        }
        
        .stylish-nav-link i {
            font-size: 0.875rem;
            opacity: 0.7;
        }
        
        .stylish-nav-link.current {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            font-weight: var(--font-weight-bold);
        }
        
        .stylish-nav-link.current::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: var(--color-black);
        }
        
        /* ===============================================
           HEADER ACTIONS
           =============================================== */
        .stylish-actions {
            display: none;
            align-items: center;
            gap: var(--space-2);
            flex-shrink: 0;
        }
        
        @media (min-width: 768px) {
            .stylish-actions {
                display: flex;
            }
        }
        
        .stylish-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-5);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-weight: var(--font-weight-semibold);
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            letter-spacing: 0.01em;
        }
        
        .stylish-btn-icon {
            width: 2.75rem;
            height: 2.75rem;
            padding: 0;
            color: var(--text-secondary);
            background: transparent;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-lg);
        }
        
        .stylish-btn-icon:hover {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            border-color: var(--border-dark);
            transform: translateY(-1px);
        }
        
        .stylish-btn-primary {
            background: var(--bg-dark);
            color: var(--text-inverse);
            border: 1px solid var(--bg-dark);
        }
        
        .stylish-btn-primary:hover {
            background: var(--color-gray-800);
            border-color: var(--color-gray-800);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* Mobile Menu Button */
        .stylish-mobile-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            color: var(--text-primary);
            background: transparent;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        
        @media (min-width: 1024px) {
            .stylish-mobile-btn {
                display: none;
            }
        }
        
        .stylish-mobile-btn:hover {
            background: var(--bg-tertiary);
            border-color: var(--border-dark);
            transform: scale(1.05);
        }
        
        /* ===============================================
           SEARCH BAR
           =============================================== */
        .stylish-search-bar {
            background: var(--bg-primary);
            border-top: 1px solid var(--border-light);
            display: none;
            transform: translateY(-20px);
            opacity: 0;
            transition: all var(--transition-base);
        }
        
        .stylish-search-bar.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }
        
        .stylish-search-form {
            padding: var(--space-8);
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }
        
        @media (min-width: 768px) {
            .stylish-search-form {
                flex-direction: row;
                align-items: center;
            }
        }
        
        .stylish-search-input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .stylish-search-input {
            width: 100%;
            padding: var(--space-4) var(--space-6) var(--space-4) 3.5rem;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-xl);
            font-size: 0.9375rem;
            transition: all var(--transition-fast);
            background: var(--bg-primary);
            color: var(--text-primary);
            font-weight: var(--font-weight-normal);
            font-family: var(--font-secondary);
        }
        
        .stylish-search-input:focus {
            outline: none;
            border-color: var(--border-dark);
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.04);
        }
        
        .stylish-search-input::placeholder {
            color: var(--text-tertiary);
        }
        
        .stylish-search-icon {
            position: absolute;
            left: var(--space-5);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-tertiary);
            font-size: 1rem;
        }
        
        .stylish-search-filters {
            display: flex;
            gap: var(--space-3);
            flex-wrap: wrap;
        }
        
        .stylish-search-select {
            padding: var(--space-4) var(--space-5);
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-xl);
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: var(--font-weight-medium);
            min-width: 150px;
            transition: all var(--transition-fast);
            cursor: pointer;
            font-family: var(--font-secondary);
        }
        
        .stylish-search-select:focus {
            outline: none;
            border-color: var(--border-dark);
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.04);
        }
        
        .stylish-search-submit {
            background: var(--bg-dark);
            color: var(--text-inverse);
            border: 1px solid var(--bg-dark);
            padding: var(--space-4) var(--space-8);
            border-radius: var(--radius-xl);
            font-weight: var(--font-weight-semibold);
            font-size: 0.875rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            white-space: nowrap;
            font-family: var(--font-secondary);
            letter-spacing: 0.01em;
        }
        
        .stylish-search-submit:hover {
            background: var(--color-gray-800);
            border-color: var(--color-gray-800);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* ===============================================
           MOBILE MENU - CIRCULAR SCROLLABLE DESIGN
           =============================================== */
        .stylish-mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-base);
            backdrop-filter: blur(4px);
        }
        
        .stylish-mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .stylish-mobile-menu {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 22rem;
            max-width: calc(100vw - 2rem);
            background: var(--bg-primary);
            transform: translateX(100%);
            transition: transform var(--transition-base);
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-xl);
            border-radius: 24px 0 0 24px;
        }
        
        .stylish-mobile-menu.show {
            transform: translateX(0);
        }
        
        .stylish-mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-6);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            background: var(--bg-primary);
            z-index: 10;
        }
        
        .stylish-mobile-title {
            font-size: 1.125rem;
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            font-family: var(--font-primary);
            letter-spacing: -0.02em;
        }
        
        .stylish-mobile-close {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            background: transparent;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        
        .stylish-mobile-close:hover {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            border-color: var(--border-dark);
            transform: rotate(90deg);
        }
        
        .stylish-mobile-search {
            padding: var(--space-6);
            border-bottom: 1px solid var(--border-light);
        }
        
        /* Circular Scrollable Navigation */
        .stylish-mobile-nav {
            padding: var(--space-6) var(--space-4);
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }
        
        .stylish-mobile-nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-4);
            padding: var(--space-5);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: var(--font-weight-semibold);
            font-size: 1rem;
            transition: all var(--transition-fast);
            border: 2px solid var(--border-light);
            border-radius: var(--radius-2xl);
            background: var(--bg-primary);
            position: relative;
            overflow: hidden;
        }
        
        .stylish-mobile-nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--color-black);
            transform: translateX(-100%);
            transition: transform var(--transition-fast);
        }
        
        .stylish-mobile-nav-link:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border-color: var(--border-dark);
            transform: translateX(4px);
        }
        
        .stylish-mobile-nav-link:hover::before {
            transform: translateX(0);
        }
        
        .stylish-mobile-nav-link.current {
            background: var(--bg-dark);
            color: var(--text-inverse);
            border-color: var(--bg-dark);
            font-weight: var(--font-weight-bold);
        }
        
        .stylish-mobile-nav-link.current::before {
            background: var(--text-inverse);
            transform: translateX(0);
        }
        
        .stylish-mobile-nav-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-tertiary);
            border-radius: var(--radius-full);
            font-size: 1.125rem;
            flex-shrink: 0;
            transition: all var(--transition-fast);
        }
        
        .stylish-mobile-nav-link:hover .stylish-mobile-nav-icon {
            background: var(--bg-dark);
            color: var(--text-inverse);
            transform: rotate(360deg);
        }
        
        .stylish-mobile-nav-link.current .stylish-mobile-nav-icon {
            background: var(--text-inverse);
            color: var(--bg-dark);
        }
        
        .stylish-mobile-nav-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .stylish-mobile-nav-main {
            font-size: 1rem;
            font-weight: var(--font-weight-semibold);
            font-family: var(--font-primary);
            letter-spacing: 0.01em;
        }
        
        .stylish-mobile-nav-sub {
            font-size: 0.6875rem;
            opacity: 0.7;
            font-weight: var(--font-weight-medium);
            letter-spacing: 0.02em;
        }
        
        .stylish-mobile-actions {
            border-top: 1px solid var(--border-light);
            padding: var(--space-6);
        }
        
        .stylish-mobile-cta {
            background: var(--bg-dark);
            color: var(--text-inverse);
            padding: var(--space-5) var(--space-6);
            border-radius: var(--radius-2xl);
            text-decoration: none;
            font-weight: var(--font-weight-bold);
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            transition: all var(--transition-fast);
            border: 2px solid var(--bg-dark);
            font-family: var(--font-primary);
            letter-spacing: 0.01em;
        }
        
        .stylish-mobile-cta:hover {
            background: var(--color-gray-800);
            border-color: var(--color-gray-800);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        /* ===============================================
           STATS DISPLAY
           =============================================== */
        .stylish-stats {
            display: none;
            align-items: center;
            gap: var(--space-5);
            font-size: 0.75rem;
            color: var(--text-tertiary);
            margin-left: var(--space-6);
            padding: var(--space-2) var(--space-5);
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-full);
            letter-spacing: 0.02em;
        }
        
        @media (min-width: 1280px) {
            .stylish-stats {
                display: flex;
            }
        }
        
        .stylish-stat-item {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            position: relative;
        }
        
        .stylish-stat-item::after {
            content: '';
            position: absolute;
            right: calc(var(--space-5) * -0.5);
            width: 1px;
            height: 60%;
            background: var(--border-medium);
        }
        
        .stylish-stat-item:last-child::after {
            display: none;
        }
        
        .stylish-stat-number {
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            font-size: 0.8125rem;
        }
        
        .stylish-stat-dot {
            width: 3px;
            height: 3px;
            background: var(--color-black);
            border-radius: 50%;
        }
        
        /* ===============================================
           UTILITY CLASSES
           =============================================== */
        .stylish-hidden {
            display: none !important;
        }
        
        .stylish-loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }
        
        .stylish-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 14px;
            height: 14px;
            margin: -7px 0 0 -7px;
            border: 2px solid var(--color-black);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Focus States */
        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        select:focus-visible {
            outline: 2px solid var(--color-black);
            outline-offset: 2px;
        }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Scrollbar Styling */
        .stylish-mobile-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .stylish-mobile-menu::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }
        
        .stylish-mobile-menu::-webkit-scrollbar-thumb {
            background: var(--color-gray-400);
            border-radius: var(--radius-full);
        }
        
        .stylish-mobile-menu::-webkit-scrollbar-thumb:hover {
            background: var(--color-gray-500);
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Announcement Bar -->
<?php if (get_theme_mod('botanist_show_announcement', false)): ?>
<div id="stylish-announcement" class="stylish-announcement">
    <?php echo esc_html(get_theme_mod('botanist_announcement_text', '最新助成金情報を随時更新中')); ?>
    <?php if ($announcement_link = get_theme_mod('botanist_announcement_link', get_post_type_archive_link('grant'))): ?>
        <a href="<?php echo esc_url($announcement_link); ?>">詳細を見る</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Main Header -->
<header id="stylish-site-header" class="stylish-header <?php echo get_theme_mod('botanist_show_announcement', false) ? 'with-announcement' : ''; ?>">
    <div class="stylish-container">
        <div class="stylish-header-inner">
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="stylish-logo" aria-label="<?php bloginfo('name'); ?> ホームページへ">
                <img src="https://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png" 
                     alt="<?php bloginfo('name'); ?>" 
                     class="stylish-logo-image"
                     width="200"
                     height="60"
                     loading="eager"
                     fetchpriority="high"
                     decoding="async">
                
                <div class="stylish-logo-text">
                    <?php if (is_front_page() || is_home()) : ?>
                        <h1><?php bloginfo('name'); ?></h1>
                    <?php else : ?>
                        <p class="site-name"><?php bloginfo('name'); ?></p>
                    <?php endif; ?>
                    <?php if ($tagline = get_bloginfo('description')): ?>
                        <p><?php echo esc_html($tagline); ?></p>
                    <?php endif; ?>
                </div>
            </a>
            
            <!-- Navigation -->
            <nav class="stylish-nav" role="navigation" aria-label="メインナビゲーション">
                <?php
                $current_url = home_url(add_query_arg(null, null));
                $home_url = home_url('/');
                $grants_url = get_post_type_archive_link('grant');
                $diagnosis_url = home_url('/subsidy-diagnosis/');
                $contact_url = home_url('/contact/');
                
                $how_to_use_url = home_url('/how-to-use/');
                
                $menu_items = array(
                    array(
                        'url' => $home_url, 
                        'title_en' => 'Home',
                        'title_ja' => 'ホーム',
                        'icon' => 'fas fa-home',
                        'current' => ($current_url === $home_url)
                    ),
                    array(
                        'url' => $how_to_use_url, 
                        'title_en' => 'How To Use',
                        'title_ja' => '使い方',
                        'icon' => 'fas fa-book-open',
                        'current' => (strpos($current_url, '/how-to-use/') !== false)
                    ),
                    array(
                        'url' => $grants_url, 
                        'title_en' => 'Grants',
                        'title_ja' => '助成金一覧',
                        'icon' => 'fas fa-list-ul',
                        'current' => (strpos($current_url, 'grants') !== false || is_post_type_archive('grant') || is_singular('grant'))
                    ),
                    array(
                        'url' => $diagnosis_url, 
                        'title_en' => 'Diagnosis',
                        'title_ja' => '診断システム',
                        'icon' => 'fas fa-stethoscope',
                        'current' => (strpos($current_url, '/subsidy-diagnosis/') !== false)
                    ),
                    array(
                        'url' => $contact_url, 
                        'title_en' => 'Contact',
                        'title_ja' => 'お問い合わせ',
                        'icon' => 'fas fa-envelope',
                        'current' => (strpos($current_url, '/contact/') !== false)
                    ),
                );
                
                foreach ($menu_items as $item) {
                    $class = 'stylish-nav-link';
                    if ($item['current']) {
                        $class .= ' current';
                    }
                    
                    echo '<a href="' . esc_url($item['url']) . '" class="' . $class . '" aria-current="' . ($item['current'] ? 'page' : 'false') . '">';
                    echo '<div class="stylish-nav-link-main">';
                    echo '<i class="' . esc_attr($item['icon']) . '"></i>';
                    echo '<span>' . esc_html($item['title_en']) . '</span>';
                    echo '</div>';
                    echo '<div class="stylish-nav-link-sub">' . esc_html($item['title_ja']) . '</div>';
                    echo '</a>';
                }
                ?>
            </nav>
            
            <!-- Header Actions -->
            <div class="stylish-actions">
                <!-- Search Toggle -->
                <button type="button" id="stylish-search-toggle" class="stylish-btn stylish-btn-icon" title="検索" aria-label="検索を開く">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Stats Display -->
                <div class="stylish-stats" aria-label="統計情報">
                    <?php
                    $stats = gi_get_cached_stats();
                    if ($stats && !empty($stats['total_grants'])) {
                        echo '<div class="stylish-stat-item">';
                        echo '<div class="stylish-stat-dot"></div>';
                        echo '<span class="stylish-stat-number">' . number_format($stats['total_grants']) . '</span>';
                        echo '<span>GRANTS</span>';
                        echo '</div>';
                        
                        if (!empty($stats['active_grants'])) {
                            echo '<div class="stylish-stat-item">';
                            echo '<div class="stylish-stat-dot"></div>';
                            echo '<span class="stylish-stat-number">' . number_format($stats['active_grants']) . '</span>';
                            echo '<span>ACTIVE</span>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
                
                <!-- CTA Button -->
                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="stylish-btn stylish-btn-primary">
                    <i class="fas fa-search"></i>
                    <span>助成金を探す</span>
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button type="button" id="stylish-mobile-menu-btn" class="stylish-mobile-btn" aria-label="メニューを開く" aria-expanded="false">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Search Bar -->
        <div id="stylish-search-bar" class="stylish-search-bar" role="search">
            <form id="stylish-search-form" class="stylish-search-form">
                <div class="stylish-search-input-wrapper">
                    <input type="text" 
                           id="stylish-search-input"
                           name="search" 
                           placeholder="助成金名、実施組織名で検索..." 
                           class="stylish-search-input"
                           autocomplete="off"
                           aria-label="検索キーワード">
                    <i class="fas fa-search stylish-search-icon" aria-hidden="true"></i>
                </div>
                
                <div class="stylish-search-filters">
                    <select name="category" class="stylish-search-select" aria-label="カテゴリー選択">
                        <option value="">カテゴリー</option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'grant_category',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 30
                        ));
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->slug) . '">';
                                echo esc_html($category->name) . ' (' . $category->count . ')';
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <select name="prefecture" class="stylish-search-select" aria-label="都道府県選択">
                        <option value="">都道府県</option>
                        <?php
                        $prefectures = get_terms(array(
                            'taxonomy' => 'grant_prefecture',
                            'hide_empty' => true,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ));
                        if ($prefectures && !is_wp_error($prefectures)) {
                            foreach ($prefectures as $prefecture) {
                                echo '<option value="' . esc_attr($prefecture->slug) . '">';
                                echo esc_html($prefecture->name) . ' (' . $prefecture->count . ')';
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <button type="submit" class="stylish-search-submit">
                        <i class="fas fa-search"></i>
                        <span>検索</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="stylish-mobile-overlay" class="stylish-mobile-overlay" role="dialog" aria-modal="true" aria-label="モバイルメニュー">
    <div id="stylish-mobile-menu" class="stylish-mobile-menu">
        <!-- Mobile Header -->
        <div class="stylish-mobile-header">
            <div class="stylish-mobile-title">Menu</div>
            <button type="button" id="stylish-mobile-close" class="stylish-mobile-close" aria-label="メニューを閉じる">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Search -->
        <div class="stylish-mobile-search">
            <div class="stylish-search-input-wrapper">
                <input type="text" 
                       placeholder="助成金を検索..." 
                       class="stylish-search-input"
                       id="stylish-mobile-search-input"
                       aria-label="モバイル検索">
                <i class="fas fa-search stylish-search-icon" aria-hidden="true"></i>
            </div>
        </div>
        
        <!-- Mobile Navigation - Circular Design -->
        <nav class="stylish-mobile-nav" aria-label="モバイルナビゲーション">
            <?php
            foreach ($menu_items as $item) {
                $class = 'stylish-mobile-nav-link';
                if ($item['current']) {
                    $class .= ' current';
                }
                
                echo '<a href="' . esc_url($item['url']) . '" class="' . $class . '" aria-current="' . ($item['current'] ? 'page' : 'false') . '">';
                echo '<div class="stylish-mobile-nav-icon">';
                echo '<i class="' . esc_attr($item['icon']) . '"></i>';
                echo '</div>';
                echo '<div class="stylish-mobile-nav-text">';
                echo '<div class="stylish-mobile-nav-main">' . esc_html($item['title_en']) . '</div>';
                echo '<div class="stylish-mobile-nav-sub">' . esc_html($item['title_ja']) . '</div>';
                echo '</div>';
                echo '</a>';
            }
            ?>
        </nav>
        
        <!-- Mobile Actions -->
        <div class="stylish-mobile-actions">
            <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="stylish-mobile-cta">
                <i class="fas fa-search"></i>
                <span>助成金を探す</span>
            </a>
            
            <?php if ($stats && !empty($stats['total_grants'])): ?>
            <div style="text-align: center; margin-top: var(--space-5); padding-top: var(--space-5); border-top: 1px solid var(--border-light); font-size: 0.75rem; color: var(--text-tertiary); letter-spacing: 0.02em;">
                <strong style="color: var(--text-primary); font-weight: var(--font-weight-bold);"><?php echo number_format($stats['total_grants']); ?></strong> GRANTS
                <?php if (!empty($stats['active_grants'])): ?>
                / <strong style="color: var(--text-primary); font-weight: var(--font-weight-bold);"><?php echo number_format($stats['active_grants']); ?></strong> ACTIVE
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
/**
 * Enhanced Header JavaScript
 * Performance Optimized & SEO Friendly
 */
(function() {
    'use strict';
    
    // Elements Cache
    const elements = {
        header: document.getElementById('stylish-site-header'),
        announcement: document.getElementById('stylish-announcement'),
        searchToggle: document.getElementById('stylish-search-toggle'),
        searchBar: document.getElementById('stylish-search-bar'),
        searchForm: document.getElementById('stylish-search-form'),
        searchInput: document.getElementById('stylish-search-input'),
        mobileSearchInput: document.getElementById('stylish-mobile-search-input'),
        mobileMenuBtn: document.getElementById('stylish-mobile-menu-btn'),
        mobileOverlay: document.getElementById('stylish-mobile-overlay'),
        mobileMenu: document.getElementById('stylish-mobile-menu'),
        mobileClose: document.getElementById('stylish-mobile-close')
    };
    
    // State Management
    const state = {
        lastScrollTop: 0,
        isSearchOpen: false,
        isMobileMenuOpen: false,
        scrollTimeout: null
    };
    
    /**
     * Optimized Scroll Handler
     */
    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Announcement Bar Logic
        if (elements.announcement) {
            if (scrollTop > 50) {
                elements.announcement.classList.add('hidden');
                elements.header.classList.add('scrolled');
            } else {
                elements.announcement.classList.remove('hidden');
                elements.header.classList.remove('scrolled');
            }
        } else if (scrollTop > 50) {
            elements.header.classList.add('scrolled');
        } else {
            elements.header.classList.remove('scrolled');
        }
        
        // Auto-hide Header on Scroll Down
        if (scrollTop > state.lastScrollTop && scrollTop > 100 && !state.isMobileMenuOpen && !state.isSearchOpen) {
            elements.header.classList.add('hidden');
        } else if (scrollTop < state.lastScrollTop) {
            elements.header.classList.remove('hidden');
        }
        
        state.lastScrollTop = scrollTop;
    }
    
    // Throttled Scroll Event
    window.addEventListener('scroll', function() {
        if (state.scrollTimeout) clearTimeout(state.scrollTimeout);
        state.scrollTimeout = setTimeout(handleScroll, 10);
    }, { passive: true });
    
    /**
     * Search Toggle
     */
    function toggleSearch() {
        state.isSearchOpen = !state.isSearchOpen;
        
        if (state.isSearchOpen) {
            elements.searchBar.classList.add('show');
            elements.searchBar.classList.remove('stylish-hidden');
            elements.header.classList.remove('hidden');
            
            setTimeout(() => elements.searchInput?.focus(), 200);
            
            if (elements.searchToggle) {
                elements.searchToggle.innerHTML = '<i class="fas fa-times"></i>';
                elements.searchToggle.title = '閉じる';
                elements.searchToggle.setAttribute('aria-label', '検索を閉じる');
            }
        } else {
            elements.searchBar.classList.remove('show');
            setTimeout(() => elements.searchBar.classList.add('stylish-hidden'), 300);
            
            if (elements.searchToggle) {
                elements.searchToggle.innerHTML = '<i class="fas fa-search"></i>';
                elements.searchToggle.title = '検索';
                elements.searchToggle.setAttribute('aria-label', '検索を開く');
            }
        }
    }
    
    elements.searchToggle?.addEventListener('click', toggleSearch);
    
    /**
     * Search Form Submission
     */
    if (elements.searchForm) {
        elements.searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.stylish-search-submit');
            if (submitBtn) {
                submitBtn.classList.add('stylish-loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>検索中</span>';
            }
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                if (value.trim()) params.append(key, value);
            }
            
            const archiveUrl = '<?php echo esc_url(get_post_type_archive_link("grant")); ?>';
            const searchUrl = archiveUrl + (params.toString() ? '?' + params.toString() : '');
            
            setTimeout(() => window.location.href = searchUrl, 300);
        });
    }
    
    /**
     * Mobile Search
     */
    if (elements.mobileSearchInput) {
        elements.mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    const archiveUrl = '<?php echo esc_url(get_post_type_archive_link("grant")); ?>';
                    window.location.href = archiveUrl + '?search=' + encodeURIComponent(query);
                }
            }
        });
    }
    
    /**
     * Mobile Menu Functions
     */
    function openMobileMenu() {
        state.isMobileMenuOpen = true;
        elements.mobileOverlay?.classList.add('show');
        elements.mobileMenu?.classList.add('show');
        elements.header.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        if (elements.mobileMenuBtn) {
            elements.mobileMenuBtn.setAttribute('aria-expanded', 'true');
        }
    }
    
    function closeMobileMenu() {
        state.isMobileMenuOpen = false;
        elements.mobileOverlay?.classList.remove('show');
        elements.mobileMenu?.classList.remove('show');
        document.body.style.overflow = '';
        
        if (elements.mobileMenuBtn) {
            elements.mobileMenuBtn.setAttribute('aria-expanded', 'false');
        }
    }
    
    elements.mobileMenuBtn?.addEventListener('click', function() {
        if (state.isMobileMenuOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });
    elements.mobileClose?.addEventListener('click', closeMobileMenu);
    
    elements.mobileOverlay?.addEventListener('click', function(e) {
        if (e.target === elements.mobileOverlay) closeMobileMenu();
    });
    
    /**
     * Keyboard Navigation
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (state.isMobileMenuOpen) closeMobileMenu();
            else if (state.isSearchOpen) toggleSearch();
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (!state.isSearchOpen) toggleSearch();
        }
    });
    
    /**
     * Adjust Main Content Margin
     */
    function adjustMainContentMargin() {
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            const headerHeight = elements.header ? elements.header.offsetHeight : 0;
            const announcementHeight = elements.announcement && !elements.announcement.classList.contains('hidden') 
                ? elements.announcement.offsetHeight : 0;
            const margin = headerHeight + announcementHeight + 24;
            mainContent.style.marginTop = margin + 'px';
        }
    }
    
    /**
     * Initialization
     */
    function init() {
        elements.searchBar?.classList.add('stylish-hidden');
        
        setTimeout(adjustMainContentMargin, 100);
        window.addEventListener('resize', adjustMainContentMargin);
        
        console.log('[✓] Enhanced Minimalist Header initialized');
        
        // Performance Monitoring
        if ('performance' in window) {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData) {
                console.log('[Performance] DOM Load:', Math.round(perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart) + 'ms');
            }
        }
    }
    
    // Initialize on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    /**
     * Global API
     */
    window.StylishHeader = {
        toggleSearch,
        openMobileMenu,
        closeMobileMenu,
        isSearchOpen: () => state.isSearchOpen,
        isMobileMenuOpen: () => state.isMobileMenuOpen,
        adjustMainContentMargin
    };
    
})();
</script>

<!-- Schema.org Structured Data for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "url": "<?php echo esc_url(home_url('/')); ?>",
    "description": "<?php echo esc_js(get_bloginfo('description')); ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "<?php echo esc_url(get_post_type_archive_link('grant')); ?>?search={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

<main id="main-content" class="stylish-main-content">
