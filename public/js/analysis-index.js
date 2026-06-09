let currentPage = typeof INITIAL_CURRENT_PAGE !== 'undefined' ? INITIAL_CURRENT_PAGE : 1;
let totalCount = typeof INITIAL_TOTAL_COUNT !== 'undefined' ? INITIAL_TOTAL_COUNT : 0;
let totalPages = Math.max(1, Math.ceil(totalCount / 15));
let lastRenderedIdSnapshot = null;
let autoRefreshTimer = null;
let autoRefreshCountdownTimer = null;
let isAutoRefreshEnabled = false;
let isAutoRefreshChecking = false;
let autoRefreshSecondsRemaining = 10;
let latestRequestId = 0;

const AUTO_REFRESH_INTERVAL_MS = 10000;
const AUTO_REFRESH_SECONDS = 10;

function getActiveFilters() {
    return {
        searchType: document.getElementById('searchType').value,
        keyword: document.getElementById('keyword').value,
        orderBy: document.getElementById('orderBy').value,
        orderDir: document.getElementById('orderDir').value,
    };
}

function getFiltersQuery() {
    const filters = getActiveFilters();
    
    return `&searchType=${encodeURIComponent(filters.searchType)}&keyword=${encodeURIComponent(filters.keyword)}&orderBy=${encodeURIComponent(filters.orderBy)}&orderDir=${encodeURIComponent(filters.orderDir)}`;
}

function buildIdSnapshot(response) {
    return JSON.stringify({
        currentPage: response.currentPage,
        totalPages: response.totalPages,
        totalCount: response.totalCount,
        limit: response.limit,
        ids: (response.ids || []).map(id => Number(id)),
    });
}

function updateAutoRefreshCountdown(text) {
    const status = document.getElementById('autoRefreshCountdown');
    if (!status) {
        return;
    }

    if (text == '') {
        status.style.display = 'none';
    } else {
        status.style.display = 'inline';
    }

    status.textContent = text;
}

document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    loadPage(1);
});

function getFetchUrl(page) {
    return `/api/analysis?page=${page}${getFiltersQuery()}`;
}

function getDeltaFetchUrl(page) {
    return `/api/analysis?page=${page}&idOnly=1${getFiltersQuery()}`;
}

function applyFetchedData(data, options = {}) {
    const { pushState = true, scrollToTop = true, updateSnapshot = true } = options;

    currentPage = data.currentPage;
    totalPages = Math.max(1, data.totalPages);
    renderTableRows(data.data);
    updatePaginationUI(data.totalCount);

    if (updateSnapshot) {
        lastRenderedIdSnapshot = buildIdSnapshot({
            currentPage: data.currentPage,
            totalPages: data.totalPages,
            totalCount: data.totalCount,
            limit: data.limit,
            ids: (data.data || []).map(row => row.id),
        });
    }
    if (pushState) {
        const url = new URL(window.location);
        url.searchParams.set('page', currentPage);

        const filters = getActiveFilters();
        url.searchParams.set('searchType', filters.searchType);
        url.searchParams.set('keyword', filters.keyword);
        url.searchParams.set('orderBy', filters.orderBy);
        url.searchParams.set('orderDir', filters.orderDir);

        window.history.pushState({ page: currentPage }, '', url);
    }

    if (scrollToTop) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function fetchAnalysisPage(page) {
    const requestId = ++latestRequestId;
    return fetch(getFetchUrl(page))
        .then(response => response.json())
        .then(data => {
            if (requestId !== latestRequestId) {
                return null;
            }

            return data;
        });
}

function fetchAnalysisIdSnapshot(page) {
    const requestId = ++latestRequestId;
    return fetch(getDeltaFetchUrl(page))
        .then(response => response.json())
        .then(data => {
            if (requestId !== latestRequestId) {
                return null;
            }

            return data;
        });
}

function stopAutoRefreshTimers() {
    if (autoRefreshTimer !== null) {
        window.clearInterval(autoRefreshTimer);
        autoRefreshTimer = null;
    }

    if (autoRefreshCountdownTimer !== null) {
        window.clearInterval(autoRefreshCountdownTimer);
        autoRefreshCountdownTimer = null;
    }
}

function beginAutoRefreshCycle() {
    if (!isAutoRefreshEnabled || isAutoRefreshChecking) {
        return;
    }

    isAutoRefreshChecking = true;
    updateAutoRefreshCountdown('...');

    fetchAnalysisIdSnapshot(currentPage)
        .then(data => {
            if (!data) {
                return null;
            }

            const nextSnapshot = buildIdSnapshot(data);
            if (nextSnapshot !== lastRenderedIdSnapshot) {
                return fetchAnalysisPage(currentPage).then(fullData => {
                    if (!fullData) {
                        return;
                    }

                    applyFetchedData(fullData, { pushState: false, scrollToTop: false, updateSnapshot: true });
                });
            }

            return null;
        })
        .catch(error => {
            console.error('Error auto-refreshing analysis data:', error);
        })
        .finally(() => {
            isAutoRefreshChecking = false;
            autoRefreshSecondsRemaining = AUTO_REFRESH_SECONDS;
            updateAutoRefreshCountdown(`(${autoRefreshSecondsRemaining})`);
        });
}

function tickAutoRefreshCountdown() {
    if (!isAutoRefreshEnabled || isAutoRefreshChecking) {
        return;
    }

    autoRefreshSecondsRemaining -= 1;
    if (autoRefreshSecondsRemaining <= 0) {
        beginAutoRefreshCycle();
        return;
    }

    updateAutoRefreshCountdown(`(${autoRefreshSecondsRemaining})`);
}

function startAutoRefresh() {
    isAutoRefreshEnabled = true;
    stopAutoRefreshTimers();
    autoRefreshSecondsRemaining = AUTO_REFRESH_SECONDS;
    updateAutoRefreshCountdown(`(${autoRefreshSecondsRemaining})`);
    autoRefreshCountdownTimer = window.setInterval(tickAutoRefreshCountdown, 1000);
}

function stopAutoRefresh() {
    isAutoRefreshEnabled = false;
    isAutoRefreshChecking = false;
    stopAutoRefreshTimers();
    autoRefreshSecondsRemaining = AUTO_REFRESH_SECONDS;
    updateAutoRefreshCountdown(``);
}

function syncAutoRefreshToggle() {
    const checkbox = document.getElementById('autoRefreshToggle');
    if (!checkbox) {
        return;
    }

    const savedValue = window.localStorage.getItem('analysisAutoRefreshEnabled');
    const shouldEnable = savedValue === 'true';
    checkbox.checked = shouldEnable;

    if (shouldEnable) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }

    checkbox.addEventListener('change', function() {
        window.localStorage.setItem('analysisAutoRefreshEnabled', String(checkbox.checked));
        if (checkbox.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
}

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
    const requestId = ++latestRequestId;

    fetch(`/api/analysis?page=${page}${filtersStr}`)
        .then(response => response.json())
        .then(data => {
            if (requestId !== latestRequestId) {
                return;
            }

            currentPage = data.currentPage;
            totalPages = Math.max(1, data.totalPages);
            renderTableRows(data.data);
            updatePaginationUI(data.totalCount);
            lastRenderedIdSnapshot = buildIdSnapshot({
                currentPage: data.currentPage,
                totalPages: data.totalPages,
                totalCount: data.totalCount,
                limit: data.limit,
                ids: (data.data || []).map(row => row.id),
            });
            
            if (pushState) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);

                const filters = getActiveFilters();
                url.searchParams.set('searchType', filters.searchType);
                url.searchParams.set('keyword', filters.keyword);
                url.searchParams.set('orderBy', filters.orderBy);
                url.searchParams.set('orderDir', filters.orderDir);

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

    if (typeof INITIAL_ANALYSIS_IDS !== 'undefined' && Array.isArray(INITIAL_ANALYSIS_IDS)) {
        lastRenderedIdSnapshot = buildIdSnapshot({
            currentPage,
            totalPages,
            totalCount,
            limit: 15,
            ids: INITIAL_ANALYSIS_IDS,
        });
    }

    syncAutoRefreshToggle();
    updatePaginationUI();
});

window.addEventListener('beforeunload', function() {
    stopAutoRefreshTimers();
});