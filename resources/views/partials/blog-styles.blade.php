<style>
    .blog-layout { display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 2rem; align-items: start; margin-top: 2rem; }
    .blog-card { background: white; border: 1px solid #e5dfee; border-radius: 1.2rem; padding: 1.5rem; margin-bottom: 1.5rem; overflow-wrap: anywhere; }
    .blog-card h2 { font-size: 1.3rem; margin: 0 0 .75rem; letter-spacing: -.025em; }
    .blog-card a:hover, .blog-article a:hover, .blog-pagination a:hover { text-decoration: underline; }
    .blog-meta { color: #746c82; font-size: .875rem; line-height: 1.7; }
    .blog-excerpt { line-height: 1.8; color: #51485e; }
    .blog-tags { display: flex; flex-wrap: wrap; gap: .5rem; margin-block: 1rem; }
    .blog-tags a { background: #eee8f8; color: #66518c; padding: .35rem .7rem; border-radius: 999px; font-size: .8rem; }
    .blog-read-more { display: inline-block; color: #66518c; font-weight: 650; margin-top: .5rem; }
    .featured { background: #eee8f8; }
    .featured h2 { margin-top: 1rem; }
    .blog-sidebar ul { list-style: none; padding: 0; margin: 0; }
    .blog-sidebar li { margin-top: .9rem; line-height: 1.5; }
    .blog-sidebar span { color: #746c82; font-size: .85rem; }
    .blog-search { margin-bottom: 1.5rem; }
    .blog-search label { display: block; font-size: .875rem; font-weight: 650; margin-bottom: .5rem; }
    .blog-search div { display: flex; gap: .6rem; }
    .blog-search input { flex: 1; min-width: 0; border: 1px solid #d6cce3; border-radius: .7rem; padding: .8rem; font: inherit; }
    .blog-search button { background: #66518c; color: white; border: 0; padding: .8rem 1rem; border-radius: .7rem; font: inherit; cursor: pointer; }
    .blog-search input:focus-visible, .blog-search button:focus-visible { outline: 3px solid #8675bd; outline-offset: 3px; }
    .blog-pagination { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding-block: 1rem; }
    .blog-pagination span { color: #746c82; }
    .blog-image { display: block; width: 100%; max-height: 420px; object-fit: cover; border-radius: .75rem; margin-bottom: 1.5rem; }
    .blog-article { max-width: 800px; margin: 2rem auto; overflow-wrap: anywhere; }
    .blog-article h1 { font-size: clamp(2rem, 5vw, 3.5rem); }
    .blog-content { white-space: pre-wrap; line-height: 1.9; font-size: 1.05rem; margin-block: 2rem; }
    @media (max-width: 800px) { .blog-layout { grid-template-columns: minmax(0, 1fr); } }
</style>
