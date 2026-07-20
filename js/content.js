// TriHard Gaming Tech - Dynamic Content Engine
// Loads and displays articles from the content pipeline

const articleData = [
    {
        id: 1,
        title: "AI Productivity Tools Every Business Needs in 2026",
        excerpt: "The landscape of AI-driven productivity is evolving. Here's what's delivering measurable ROI right now.",
        category: "AI",
        date: "2026-07-19",
        readTime: "6 min read",
        featured: true
    },
    {
        id: 2,
        title: "Remote Work Trends: Data-Driven Insights for 2026",
        excerpt: "Remote work isn't dying—it's maturing. The data tells a compelling story about where work is headed.",
        category: "Workplace",
        date: "2026-07-19",
        readTime: "5 min read",
        featured: true
    },
    {
        id: 3,
        title: "SaaS Marketing Strategies That Actually Convert",
        excerpt: "Forget vanity metrics. These 5 SaaS marketing strategies drive real revenue growth.",
        category: "Marketing",
        date: "2026-07-19",
        readTime: "7 min read",
        featured: false
    }
];

// Render articles dynamically
function renderArticles() {
    const grid = document.querySelector('.article-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    articleData.forEach(article => {
        const card = document.createElement('div');
        card.className = 'article-card';
        card.innerHTML = `
            <span class="category-badge">${article.category}</span>
            <h3>${article.title}</h3>
            <p>${article.excerpt}</p>
            <div class="article-meta">
                <span>${article.date}</span>
                <span>${article.readTime}</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        `;
        grid.appendChild(card);
    });
}

// Newsletter subscription handler
function handleSubscription(event) {
    event.preventDefault();
    const email = document.querySelector('.newsletter-form input').value;
    if (email) {
        // TODO: Connect to email API
        console.log('Subscribing:', email);
        alert('Thanks for subscribing! You\'ll receive weekly briefings.');
        document.querySelector('.newsletter-form input').value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    renderArticles();
    const form = document.querySelector('.newsletter-form');
    if (form) {
        form.addEventListener('submit', handleSubscription);
    }
});

// Style the category badges
const style = document.createElement('style');
style.textContent = `
    .category-badge {
        background-color: var(--accent);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        text-transform: uppercase;
    }
    .article-meta {
        display: flex;
        justify-content: space-between;
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 1rem 0;
    }
`;
document.head.appendChild(style);
