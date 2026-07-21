#!/usr/bin/env python3
"""
Auto-generate homepage (index.html) and articles index (articles/index.html)
from the articles/ directory. Parses each article HTML for metadata, sorts
by date (newest first), dynamically categorizes, commits and pushes to GitHub.

Usage:
    python regenerate_homepage.py [--dry-run]
"""

import os
import re
import subprocess
import sys
from collections import defaultdict
from datetime import datetime
from pathlib import Path

SITE_DIR = Path(__file__).parent
ARTICLES_DIR = SITE_DIR / "articles"
HOMEPAGE_FILE = SITE_DIR / "index.html"
ARTICLES_INDEX_FILE = ARTICLES_DIR / "index.html"

# Max articles to show on homepage
MAX_HOMEPAGE_ARTICLES = 12

# Category grouping map: raw category -> display section
CATEGORY_GROUPS = {
    "original": {
        "label": "Original Analysis",
        "css_class": "",
        "matches": ["original analysis", "original"],
    },
    "ai": {
        "label": "AI",
        "css_class": "",
        "matches": ["ai", "ai music", "ai news", "ai industry", "ai business",
                    "ai infrastructure", "ai / dev tools", "ai / developer tools",
                    "ai / dev tools"],
    },
    "gaming": {
        "label": "Gaming",
        "css_class": "gaming",
        "matches": ["gaming", "gaming hardware"],
    },
    "computing": {
        "label": "Computer Science",
        "css_class": "",
        "matches": ["computer science", "computing / science", "computing"],
    },
    "space": {
        "label": "Space",
        "css_class": "",
        "matches": ["space exploration", "space"],
    },
    "tech": {
        "label": "Tech",
        "css_class": "tech",
        "matches": ["tech review", "tech news", "tech"],
    },
}


def normalize_category(raw: str) -> str:
    """Return the display section key for a raw category string."""
    raw_lower = raw.lower().strip()
    for section_key, info in CATEGORY_GROUPS.items():
        for match_str in info["matches"]:
            if match_str in raw_lower:
                return section_key
    # Default: put "original" articles in the original section,
    # everything else in "tech" as a catch-all
    if "original" in raw_lower:
        return "original"
    return "tech"


def parse_article_metadata(filepath: Path) -> dict | None:
    """Extract title, category, date, author, and lead from an article HTML file."""
    try:
        content = filepath.read_text(encoding="utf-8")
    except Exception as e:
        print(f"  ⚠ Failed to read {filepath.name}: {e}")
        return None

    # Extract title from <h1> tag
    h1_match = re.search(r"<h1>(.*?)</h1>", content, re.DOTALL)
    if not h1_match:
        return None
    title = re.sub(r"<[^>]+>", "", h1_match.group(1)).strip()

    # Extract raw category from article-category or category-tag
    raw_category = "General"
    cat_match = re.search(r'class="(?:article-)?category[^\"]*"\s*>([^<]+)', content)
    if cat_match:
        raw_category = cat_match.group(1).strip()
    tag_match = re.search(r'class="(?:article-)?tag[^\"]*"\s*>([^<]+)', content)
    if tag_match:
        raw_category = tag_match.group(1).strip()

    # Normalize to display section
    display_section = normalize_category(raw_category)

    # Extract date from time[datetime=...] or article-meta
    date = datetime.now()
    time_match = re.search(r'datetime="([\d-]+)"', content)
    if time_match:
        try:
            date = datetime.strptime(time_match.group(1), "%Y-%m-%d")
        except ValueError:
            pass
    # Fallback: parse "Published: ... · Month Day, Year" or "article-meta ... · Month Day, Year"
    if date == datetime.now().replace(hour=0, minute=0, second=0, microsecond=0) or True:
        date_str_match = re.search(r'(?:Published:.*?|article-meta[^>]*>[^·]*·\s*)(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{1,2}),\s*(\d{4})', content, re.DOTALL)
        if date_str_match:
            try:
                date = datetime.strptime(f"{date_str_match.group(1)} {date_str_match.group(2)}, {date_str_match.group(3)}", "%B %d, %Y")
            except ValueError:
                pass

    # Extract author
    author = "Unknown"
    m2 = re.search(r'class="author[^\"]*"[^>]*>([^<]+)', content)
    if m2:
        author = m2.group(1).strip()
    else:
        author_match = re.search(r'By\s+([^.·<]+)', content, re.DOTALL)
        if author_match:
            author = author_match.group(1).strip()

    # Extract lead/summary paragraph (first <p> with class="lead" or first <p> after header)
    summary = ""
    lead_match = re.search(r'<p\s+class="lead"[^>]*>(.*?)</p>', content, re.DOTALL)
    if lead_match:
        summary = re.sub(r"<[^>]+>", "", lead_match.group(1)).strip()
    else:
        # Fallback: first <p> in article-content
        content_match = re.search(r'class="article-content"[^>]*>(.*?)</main>', content, re.DOTALL)
        if content_match:
            p_match = re.search(r'<p[^>]*>(.*?)</p>', content_match.group(1), re.DOTALL)
            if p_match:
                summary = re.sub(r"<[^>]+>", "", p_match.group(1)).strip()

    # Truncate summary to 160 chars for homepage cards
    if len(summary) > 160:
        summary = summary[:157] + "..."

    # Get the file's href (relative path from homepage or articles index)
    base_name = filepath.stem
    home_href = f"articles/{base_name}.html"
    articles_href = f"{base_name}.html"

    return {
        "title": title,
        "raw_category": raw_category,
        "display_section": display_section,
        "date": date,
        "author": author,
        "summary": summary,
        "home_href": home_href,
        "articles_href": articles_href,
        "filepath": filepath,
    }


def generate_homepage(articles: list[dict]) -> str:
    """Generate index.html homepage from article list."""
    articles_sorted = sorted(articles, key=lambda a: a["date"], reverse=True)
    featured = articles_sorted[:MAX_HOMEPAGE_ARTICLES]

    cards_html = []
    for i, article in enumerate(featured):
        date_str = article["date"].strftime("%Y-%m-%d")
        display_date = article["date"].strftime("%b %d, %Y")
        is_featured = (i == 0)

        card_class = "article-card featured" if is_featured else "article-card"
        cards_html.append(f"""        <article class="{card_class}">
            <div class="card-content">
                <span class="category-tag">{article['raw_category']}</span>
                <h2><a href="{article['home_href']}">{article['title']}</a></h2>
                <p>{article['summary']}</p>
                <div class="card-meta">
                    <time datetime="{date_str}">{display_date}</time>
                    <span class="author">{article['author']}</span>
                </div>
            </div>
        </article>""")

    cards_block = "\n\n".join(cards_html)

    html = f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriHard Gaming Tech - AI &amp; Technology News</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="site-nav">
        <a href="index.html" class="logo">TriHard Gaming Tech</a>
        <div class="nav-links">
            <a href="index.html" class="active">Home</a>
            <a href="articles/index.html">Articles</a>
            <a href="about/index.html">About</a>
            <a href="contact/index.html">Contact</a>
        </div>
    </nav>

    <header class="hero">
        <h1>AI &amp; Technology News</h1>
        <p>Curated coverage of artificial intelligence, emerging tech, and the industry moves shaping our future.</p>
        <a href="articles/index.html" class="hero-cta">Read Latest Articles</a>
    </header>

    <main class="articles-grid">
{cards_block}

        <div class="article-card view-all-card">
            <a href="articles/index.html" class="view-all-link">View All Articles →</a>
        </div>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 TriHard Gaming Tech. All rights reserved.</p>
        <p><a href="about/index.html">About</a> &middot; <a href="contact/index.html">Contact</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>"""
    return html


def generate_articles_index(articles: list[dict]) -> str:
    """Generate articles/index.html grouped by category with proper section headers."""
    articles_sorted = sorted(articles, key=lambda a: a["date"], reverse=True)

    # Group by display section
    groups = defaultdict(list)
    for article in articles_sorted:
        groups[article["display_section"]].append(article)

    # Section render order (original first, then alphabetical)
    section_order = ["original", "ai", "computing", "space", "gaming", "tech"]
    # Add any dynamic sections not in the predefined list
    for section_key in groups:
        if section_key not in section_order:
            section_order.append(section_key)

    # Build sections HTML
    sections_html = []
    for section_key in section_order:
        if section_key not in groups:
            continue

        section_info = CATEGORY_GROUPS.get(section_key, {"label": section_key.title(), "css_class": ""})
        label = section_info["label"]
        css_class = section_info["css_class"]

        articles_html = []
        for i, article in enumerate(groups[section_key]):
            date_str = article["date"].strftime("%Y-%m-%d")
            display_date = article["date"].strftime("%b %d, %Y")
            is_first = (i == 0 and section_key == "original")

            card_class = "article-card article-list-card featured" if is_first else "article-card article-list-card"
            articles_html.append(f"""
            <article class="{card_class}">
                <a href="{article['articles_href']}" class="card-link">
                    <span class="category-tag">{article['raw_category']}</span>
                    <h3>{article['title']}</h3>
                    <p>{article['summary']}</p>
                    <div class="card-meta">
                        <time datetime="{date_str}">{display_date}</time>
                        <span class="author">{article['author']}</span>
                    </div>
                </a>
            </article>""")

        badge_style = f'style="background:var(--accent);"' if section_key == "original" else ""
        section_html = f"""
        <section class="articles-list" data-category="{section_key}">
            <h2><span class="section-badge" {badge_style}>{label}</span> {label}</h2>
{"".join(articles_html)}
        </section>"""
        sections_html.append(section_html)

    sections_block = "\n".join(sections_html)

    html = f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Articles - TriHard Gaming Tech</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="site-nav">
        <a href="../index.html" class="logo">TriHard Gaming Tech</a>
        <div class="nav-links">
            <a href="../index.html">Home</a>
            <a href="index.html" class="active">Articles</a>
            <a href="../about/index.html">About</a>
        </div>
    </nav>

    <main class="content-wrapper">
        <h1>All Articles</h1>
        <p class="section-intro">Curated coverage of AI, gaming, tech reviews, and the industry moves shaping our future.</p>
{sections_block}
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 TriHard Gaming Tech. All rights reserved.</p>
        <p><a href="../index.html">Back to Home</a></p>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html>"""
    return html


def main():
    dry_run = "--dry-run" in sys.argv

    print(f"📰 TriHard Gaming Tech — Site Regenerator")
    print(f"{'(DRY RUN — no writes)' if dry_run else ''}")
    print(f"{'=' * 50}")

    # Scan article files (skip index.html)
    article_files = sorted(ARTICLES_DIR.glob("*.html"))
    article_files = [f for f in article_files if f.name != "index.html"]

    print(f"\n📂 Scanning {len(article_files)} article files...")

    articles = []
    for af in article_files:
        meta = parse_article_metadata(af)
        if meta:
            articles.append(meta)
            print(f"  ✓ {af.name} — {meta['title'][:60]}...")
        else:
            print(f"  ✗ {af.name} — no metadata found, skipped")

    if not articles:
        print("\n❌ No articles found with metadata. Aborting.")
        return 1

    print(f"\n✅ Parsed {len(articles)} articles total.")

    # Show category distribution
    from collections import Counter
    cat_counts = Counter(a["display_section"] for a in articles)
    print(f"\n📊 Category distribution:")
    for section_key, count in sorted(cat_counts.items(), key=lambda x: -x[1]):
        label = CATEGORY_GROUPS.get(section_key, {}).get("label", section_key)
        print(f"   {label:25s}: {count}")

    # Generate homepage HTML
    home_html = generate_homepage(articles)

    # Generate articles index HTML
    articles_html = generate_articles_index(articles)

    # Show what would be featured on homepage
    articles_sorted = sorted(articles, key=lambda a: a["date"], reverse=True)
    print(f"\n📋 Homepage features (newest first, top {MAX_HOMEPAGE_ARTICLES}):")
    for i, a in enumerate(articles_sorted[:MAX_HOMEPAGE_ARTICLES]):
        marker = "⭐" if i == 0 else "  "
        print(f"   {marker} {a['date'].strftime('%Y-%m-%d')} | {a['raw_category']:25s} | {a['title'][:55]}")

    # Write files
    if dry_run:
        print(f"\n✅ DRY RUN — would regenerate 2 pages ({len(home_html)} + {len(articles_html)} bytes).")
        return 0

    HOMEPAGE_FILE.write_text(home_html, encoding="utf-8")
    ARTICLES_INDEX_FILE.write_text(articles_html, encoding="utf-8")
    print(f"\n✅ Wrote {HOMEPAGE_FILE} ({len(home_html)} bytes).")
    print(f"✅ Wrote {ARTICLES_INDEX_FILE} ({len(articles_html)} bytes).")

    # Git commit and push
    print(f"\n🚀 Committing and pushing to GitHub...")
    try:
        subprocess.run(["git", "add", "."], cwd=SITE_DIR, check=True, capture_output=True)
        subprocess.run(
            ["git", "commit", "-m", f"Auto-regenerate site: {len(articles)} articles, {len(cat_counts)} categories, newest: {articles_sorted[0]['date'].strftime('%Y-%m-%d')}"],
            cwd=SITE_DIR, check=True, capture_output=True
        )
        result = subprocess.run(
            ["git", "push"], cwd=SITE_DIR, check=True, capture_output=True, text=True
        )
        print("   ✅ Pushed to GitHub (GitHub Pages deploying...).")
        print("\n🎉 Site updated and deployed!")
        return 0
    except subprocess.CalledProcessError as e:
        stderr = e.stderr.decode().strip()
        # "nothing added" is not an error — no changes needed
        if "nothing added" in stderr or "nothing to commit" in stderr:
            print("   ✅ No changes detected — site already up to date.")
            return 0
        print(f"   ⚠ Git error: {stderr}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
