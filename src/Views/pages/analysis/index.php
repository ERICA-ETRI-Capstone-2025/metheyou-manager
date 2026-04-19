<div class="analysis-page-container">
    <div class="analysis-header">
        <h2 class="analysis-title">분석 DB 목록</h2>
        <!-- <p class="analysis-subtitle"></p> -->
    </div>

    <div class="analysis-list-box">
        <!-- Top Pagination Info -->
        <div class="pagination-top-bar">
            <div>
                <span class="total-count-text">총 <span id="totalCountDisplay" class="total-count-number"><?= $totalCount ?></span>개 분석됨</span>
            </div>
            <div class="pagination-controls">
                <button id="topPrevBtn" class="pagination-btn" onclick="goToPreviousPage()">
                    <i class="bx bx-chevron-left"></i>
                </button>
                <span class="pagination-current-state">
                    <span id="currentPageDisplay">1</span> / <span id="totalPagesDisplay">1</span>
                </span>
                <button id="topNextBtn" class="pagination-btn" onclick="goToNextPage()">
                    <i class="bx bx-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table is-fullwidth">
                <thead class="analysis-table-header">
                    <tr>
                        <th>Thumbnail</th>
                        <th>Video ID</th>
                        <th>Title</th>
                        <th>Channel</th>
                        <th style="text-align: center;">Score</th>
                        <th>Date</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody class="analysis-table-body" id="analysisTableBody">
                    <?php foreach ($analyses as $row): ?>
                    <tr>
                        <td>
                            <a href="/analysis/detail?id=<?= $row['id'] ?>&page=<?= $currentPage ?>" class="thumbnail-link">
                                <img src="https://img.youtube.com/vi/<?= htmlspecialchars($row['youtube_video_id']) ?>/maxresdefault.jpg" alt="<?= htmlspecialchars($row['title'] ?? 'N/A') ?>" class="thumbnail-img">
                            </a>
                        </td>
                        <td>
                            <a href="https://youtube.com/watch?v=<?= htmlspecialchars($row['youtube_video_id']) ?>" target="_blank" class="video-id-link">
                                <?= htmlspecialchars($row['youtube_video_id']) ?>
                                <i class="bx bx-up-arrow-alt video-id-icon"></i>
                            </a>
                        </td>
                        <td title="<?= htmlspecialchars($row['title'] ?? 'N/A') ?>" style="max-width: 250px;">
                            <?= htmlspecialchars($row['title'] ?? 'N/A') ?>
                        </td>
                        <td title="<?= htmlspecialchars($row['channel_name'] ?? 'N/A') ?>" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($row['channel_name'] ?? 'N/A') ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($row['score'] >= 70): ?>
                                <span class="score-tag success"><?= htmlspecialchars($row['score']) ?></span>
                            <?php elseif($row['score'] >= 30): ?>
                                <span class="score-tag warning"><?= htmlspecialchars($row['score']) ?></span>
                            <?php else: ?>
                                <span class="score-tag danger"><?= htmlspecialchars($row['score']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="analysis-date">
                            <?= htmlspecialchars(date('M j, Y', strtotime($row['created_at']))) ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="/analysis/detail?id=<?= $row['id'] ?>&page=<?= $currentPage ?>" class="action-button">
                                <i class="bx bx-show"></i>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($analyses)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bx bx-folder-open empty-state-icon"></i>
                                <p class="empty-state-text">No analysis results found.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination Controls -->
        <div class="pagination-bottom-bar">
            <button class="page-nav-btn" onclick="goToFirstPage()" id="firstPageBtn">
                <i class="bx bx-chevrons-left"></i>
            </button>
            <button class="page-nav-btn" onclick="goToPreviousPage()" id="bottomPrevBtn">
                <i class="bx bx-chevron-left"></i>
            </button>
            <div id="pageNumberContainer" class="page-number-container"></div>
            <button class="page-nav-btn" onclick="goToNextPage()" id="bottomNextBtn">
                <i class="bx bx-chevron-right"></i>
            </button>
            <button class="page-nav-btn" onclick="goToLastPage()" id="lastPageBtn">
                <i class="bx bx-chevrons-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
let currentPage = <?= $currentPage ?>;
let totalPages = Math.ceil(<?= $totalCount ?> / 15);

function renderTableRows(data) {
    const tbody = document.getElementById('analysisTableBody');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="bx bx-folder-open empty-state-icon"></i>
                        <p class="empty-state-text">No analysis results found.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    data.forEach(row => {
        const scoreClass = row.score >= 70 ? 'success' : (row.score >= 30 ? 'warning' : 'danger');
        const date = new Date(row.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <a href="/analysis/detail?id=${row.id}&page=${currentPage}" class="thumbnail-link">
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
                <a href="/analysis/detail?id=${row.id}&page=${currentPage}" class="action-button">
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

function updatePaginationUI() {
    document.getElementById('currentPageDisplay').textContent = currentPage;
    document.getElementById('totalPagesDisplay').textContent = totalPages;
    
    const topPrevBtn = document.getElementById('topPrevBtn');
    const topNextBtn = document.getElementById('topNextBtn');
    const bottomPrevBtn = document.getElementById('bottomPrevBtn');
    const bottomNextBtn = document.getElementById('bottomNextBtn');
    const firstPageBtn = document.getElementById('firstPageBtn');
    const lastPageBtn = document.getElementById('lastPageBtn');
    
    topPrevBtn.disabled = currentPage === 1;
    topNextBtn.disabled = currentPage === totalPages;
    bottomPrevBtn.disabled = currentPage === 1;
    bottomNextBtn.disabled = currentPage === totalPages;
    firstPageBtn.disabled = currentPage === 1;
    lastPageBtn.disabled = currentPage === totalPages;
    
    renderPageNumbers();
}

function loadPage(page, pushState = true) {
    fetch(`/api/analysis?page=${page}`)
        .then(response => response.json())
        .then(data => {
            currentPage = data.currentPage;
            totalPages = data.totalPages;
            renderTableRows(data.data);
            updatePaginationUI();
            
            if (pushState) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                window.history.pushState({ page: page }, '', url);
            }
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error loading page:', error);
            alert('Failed to load data. Please try again.');
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

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    const page = event.state ? event.state.page : (new URL(window.location)).searchParams.get('page') || 1;
    loadPage(Number(page), false);
});

// Initialize pagination UI on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = new URL(window.location);
    if (!currentUrl.searchParams.has('page') && currentPage > 1) {
        // Just sync URL visually if first load came with php variables but no param
        currentUrl.searchParams.set('page', currentPage);
        window.history.replaceState({ page: currentPage }, '', currentUrl);
    } else {
        window.history.replaceState({ page: currentPage }, '', currentUrl);
    }
    updatePaginationUI();
});
</script>