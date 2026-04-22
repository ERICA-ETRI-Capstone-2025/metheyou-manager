let currentPage = typeof INITIAL_CURRENT_PAGE !== 'undefined' ? INITIAL_CURRENT_PAGE : 1;
let totalCount = typeof INITIAL_TOTAL_COUNT !== 'undefined' ? INITIAL_TOTAL_COUNT : 0;
let totalPages = Math.max(1, Math.ceil(totalCount / 15));

function getFiltersQuery() {
    const searchType = document.getElementById('searchType').value;
    const keyword = document.getElementById('keyword').value;
    const orderBy = document.getElementById('orderBy').value;
    const orderDir = document.getElementById('orderDir').value;
    
    return `&searchType=${encodeURIComponent(searchType)}&keyword=${encodeURIComponent(keyword)}&orderBy=${encodeURIComponent(orderBy)}&orderDir=${encodeURIComponent(orderDir)}`;
}

document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    loadPage(1);
});

function renderTableRows(data) {
    const tbody = document.getElementById('analysisTableBody');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="bx bx-folder-open empty-state-icon"></i>
                        <p class="empty-state-text">분석 결과가 없습니다.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    data.forEach(row => {
        const scoreClass = row.score >= 70 ? 'success' : (row.score >= 30 ? 'warning' : 'danger');
        const date = new Date(row.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        
        const searchType = document.getElementById('searchType').value;
        const keyword = document.getElementById('keyword').value;
        const orderBy = document.getElementById('orderBy').value;
        const orderDir = document.getElementById('orderDir').value;
        
        let queryStr = `page=${currentPage}`;
        if(searchType) queryStr += `&searchType=${encodeURIComponent(searchType)}`;
        if(keyword !== '') queryStr += `&keyword=${encodeURIComponent(keyword)}`;
        if(orderBy) queryStr += `&orderBy=${encodeURIComponent(orderBy)}`;
        if(orderDir) queryStr += `&orderDir=${encodeURIComponent(orderDir)}`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <a href="/analysis/detail?id=${row.id}&${queryStr}" class="thumbnail-link">
                    <img src="https://img.youtube.com/vi/${row.youtube_video_id}/maxresdefault.jpg" alt="${row.title || 'N/A'}" class="thumbnail-img">
                </a>
            </td>
            <td>
                <a href="https://youtube.com/watch?v=${row.youtube_video_id}" target="_blank" class="video-id-link">
                    ${row.youtube_video_id}
                    <i class="bx bx-up-arrow-alt video-id-icon"></i>
                </a>
            </td>
            <td title="${row.title || 'N/A'}" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${row.title || 'N/A'}
            </td>
            <td title="${row.channel_name || 'N/A'}" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${row.channel_name || 'N/A'}
            </td>
            <td style="text-align: center;">
                <span class="score-tag ${scoreClass}">${row.score}</span>
            </td>
            <td class="analysis-date">
                ${date}
            </td>
            <td style="text-align: center;">
                <a href="/analysis/detail?id=${row.id}&${queryStr}" class="action-button">
                    <i class="bx bx-show"></i>
                    <span>View</span>
                </a>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPageNumbers() {
    const container = document.getElementById('pageNumberContainer');
    container.innerHTML = '';
    
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.className = 'page-number' + (i === currentPage ? ' active' : '');
        btn.textContent = i;
        btn.onclick = () => loadPage(i);
        container.appendChild(btn);
    }
}

function updatePaginationUI(totalCountParam = null) {
    if (totalCountParam !== null) {
        document.getElementById('totalCountDisplay').textContent = totalCountParam;
    }
    document.getElementById('currentPageDisplay').textContent = currentPage;
    document.getElementById('totalPagesDisplay').textContent = totalPages;
    
    const topPrevBtn = document.getElementById('topPrevBtn');
    const topNextBtn = document.getElementById('topNextBtn');
    const bottomPrevBtn = document.getElementById('bottomPrevBtn');
    const bottomNextBtn = document.getElementById('bottomNextBtn');
    const firstPageBtn = document.getElementById('firstPageBtn');
    const lastPageBtn = document.getElementById('lastPageBtn');
    
    topPrevBtn.disabled = currentPage === 1 || totalPages <= 1;
    topNextBtn.disabled = currentPage === totalPages || totalPages === 0;
    bottomPrevBtn.disabled = currentPage === 1 || totalPages <= 1;
    bottomNextBtn.disabled = currentPage === totalPages || totalPages === 0;
    firstPageBtn.disabled = currentPage === 1 || totalPages <= 1;
    lastPageBtn.disabled = currentPage === totalPages || totalPages === 0;
    
    renderPageNumbers();
}

function loadPage(page, pushState = true) {
    const filtersStr = getFiltersQuery();
    fetch(`/api/analysis?page=${page}${filtersStr}`)
        .then(response => response.json())
        .then(data => {
            currentPage = data.currentPage;
            totalPages = Math.max(1, data.totalPages);
            renderTableRows(data.data);
            updatePaginationUI(data.totalCount);
            
            if (pushState) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                
                const searchType = document.getElementById('searchType').value;
                const keyword = document.getElementById('keyword').value;
                const orderBy = document.getElementById('orderBy').value;
                const orderDir = document.getElementById('orderDir').value;
                
                url.searchParams.set('searchType', searchType);
                url.searchParams.set('keyword', keyword);
                url.searchParams.set('orderBy', orderBy);
                url.searchParams.set('orderDir', orderDir);

                window.history.pushState({ page: page }, '', url);
            }
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error loading page:', error);
            alert('Failed to load analysis data. Please try again.');
        });
}

function goToFirstPage() {
    if (currentPage > 1) {
        loadPage(1);
    }
}

function goToPreviousPage() {
    if (currentPage > 1) {
        loadPage(currentPage - 1);
    }
}

function goToNextPage() {
    if (currentPage < totalPages) {
        loadPage(currentPage + 1);
    }
}

function goToLastPage() {
    if (currentPage < totalPages) {
        loadPage(totalPages);
    }
}

window.addEventListener('popstate', function(event) {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 1;
    
    // URL에 파라미터가 있으면 폼 필드 상태도 동기화합니다.
    if (params.has('searchType')) {
        document.getElementById('searchType').value = params.get('searchType');
    }
    if (params.has('keyword')) {
        document.getElementById('keyword').value = params.get('keyword');
    }
    if (params.has('orderBy')) {
        document.getElementById('orderBy').value = params.get('orderBy');
    }
    if (params.has('orderDir')) {
        document.getElementById('orderDir').value = params.get('orderDir');
    }
    
    loadPage(Number(page), false);
});

document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = new URL(window.location);
    if (!currentUrl.searchParams.has('page') && currentPage > 1) {
        currentUrl.searchParams.set('page', currentPage);
        window.history.replaceState({ page: currentPage }, '', currentUrl);
    }
    updatePaginationUI();
});