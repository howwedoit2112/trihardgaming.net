#!/usr/bin/env python3
"""
Auto-generate index.html homepage from articles/ directory.
Parses each article HTML for metadata, sorts by date (newest first),
generates homepage with top 12 articles, commits and pushes to GitHub.

Usage:
    python regenerate_homepage.py [--dry-run]
"""

import os
import re
import subprocess
import sys
from datetime import datetime
from pathlib import Path

SITE_DIR = Path(__file__).parent
ARTICLES_DIR = SITE_DIR / "articles"
INDEX_FILE = SITE_DIR / "index.html"

# Max articles to show on homepage
MAX_HOMEPAGE_ARTICLES = 12


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

    # Extract category from article-category or category-tag
    category = "General"
    cat_match = re.search(r'class="(?:article-)?category[^"]*"\s*>([^<]+)', content)
    if cat_match:
        category = cat_match.group(1).strip()
    tag_match = re.search(r'class="(?:article-)?tag[^"]*"\s*>([^<]+)', content)
    if tag_match:
        category = tag_match.group(1).strip()

    # Extract date from time[datetime=...] or article-meta
    date = datetime.now()
    time_match = re.search(r'datetime="([\d-]+)"', content)
    if time_match:
        try:
            date = datetime.strptime(time_match.group(1), "%Y-%m-%d")
        except ValueError:
            pass

    # Extract author
    author = "Unknown"
    author_match = re.search(r'By\s+(?:[^<]*?)·|class="author[^"]*"[^>]*>([^<]+)', content, re.DOTALL)
    if author_match:
        # Try to get from .author class first
        m2 = re.search(r'class="author[^"]*"[^>]*>([^<]+)', content)
        if m2:
            author = m2.group(1).strip()
        else:
            author = author_match.group(0).replace("By ", "").replace("·", "").strip()

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

    # Get the file's href (relative path from homepage)
    href = f"articles/{filepath.name.replace('.html', '')}.html"

    return {
        "title": title,
        "category": category,
        "date": date,
        "author": author,
        "summary": summary,
        "href": href,
        "filepath": filepath,
    }


def generate_homepage(articles: list[dict]) -> str:
    """Generate index.html HTML from article list."""
    # Sort by date descending (newest first)
    articles_sorted = sorted(articles, key=lambda a: a["date"], reverse=True)

    # Limit to max homepage articles
    featured = articles_sorted[:MAX_HOMEPAGE_ARTICLES]

    # Build article cards
    cards_html = []
    for i, article in enumerate(featured):
        date_str = article["date"].strftime("%Y-%m-%d")
        display_date = article["date"].strftime("%b %d, %Y")
        is_featured = (i == 0)  # First article gets featured styling

        card_class = "article-card featured" if is_featured else "article-card"
        cards_html.append(f"""        <article class="{card_class}">
            <div class="card-content">
                <span class="category-tag">{article['category']}</span>
                <h2><a href="{article['href']}">{article['title']}</a></h2>
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


def main():
    dry_run = "--dry-run" in sys.argv

    print(f"📰 TriHard Gaming Tech — Homepage Regenerator")
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
    print(f"   Will feature {min(len(articles), MAX_HOMEPAGE_ARTICLES)} on homepage.")

    # Generate HTML
    html = generate_homepage(articles)

    # Show what would be featured
    articles_sorted = sorted(articles, key=lambda a: a["date"], reverse=True)
    print(f"\n📋 Featured articles (newest first):")
    for i, a in enumerate(articles_sorted[:MAX_HOMEPAGE_ARTICLES]):
        marker = "⭐" if i == 0 else "  "
        print(f"   {marker} {a['date'].strftime('%Y-%m-%d')} | {a['category']:20s} | {a['title'][:65]}")

    # Write file
    if dry_run:
        print(f"\n✅ DRY RUN — homepage would be generated with {min(len(articles), MAX_HOMEPAGE_ARTICLES)} articles.")
        print(f"   ({len(html)} bytes of HTML)")
        return 0

    INDEX_FILE.write_text(html, encoding="utf-8")
    print(f"\n✅ Wrote {INDEX_FILE} ({len(html)} bytes).")

    # Git commit and push
    print(f"\n🚀 Committing and pushing to GitHub...")
    try:
        subprocess.run(["git", "add", "index.html"], cwd=SITE_DIR, check=True, capture_output=True)
        subprocess.run(
            ["git", "commit", "-m", f"Auto-update homepage: {len(articles)} articles, newest: {articles_sorted[0]['date'].strftime('%Y-%m-%d')}"],
            cwd=SITE_DIR, check=True, capture_output=True
        )
        result = subprocess.run(
            ["git", "push"], cwd=SITE_DIR, check=True, capture_output=True, text=True
        )
        print("   ✅ Pushed to GitHub (GitHub Pages deploying...).")
        print("\n🎉 Homepage updated and deployed!")
        return 0
    except subprocess.CalledProcessError as e:
        print(f"   ⚠ Git error: {e.stderr.decode().strip()}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
