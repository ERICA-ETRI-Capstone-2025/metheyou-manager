<div style="padding-bottom: 2rem;">
    <div class="analysis-detail-container">
        <!-- Top Actions -->
        <div class="top-actions">
            <!-- Back Button -->
            <a href="/analysis?<?= htmlspecialchars($queryString ?? 'page='.($page ?? 1)) ?>" class="back-button">
                <i class="bx bx-arrow-back"></i>
                <span>목록으로</span>
            </a>
            
            <!-- Delete Button -->
            <form action="/analysis/delete" method="POST" class="delete-form" onsubmit="return confirm('이 분석 결과를 정말 삭제하시겠습니까?\nMariaDB(analysis, tasks, feedback)와 PostgreSQL(벡터DB)의 모든 관련 데이터가 영구적으로 삭제됩니다.');">
                <input type="hidden" name="id" value="<?= htmlspecialchars($analysis['id']) ?>">
                <input type="hidden" name="page" value="<?= htmlspecialchars($page ?? 1) ?>">
                <?php if(isset($searchParams)): ?>
                <input type="hidden" name="searchType" value="<?= htmlspecialchars($searchParams['searchType'] ?? '') ?>">
                <input type="hidden" name="keyword" value="<?= htmlspecialchars($searchParams['keyword'] ?? '') ?>">
                <input type="hidden" name="orderBy" value="<?= htmlspecialchars($searchParams['orderBy'] ?? '') ?>">
                <input type="hidden" name="orderDir" value="<?= htmlspecialchars($searchParams['orderDir'] ?? '') ?>">
                <?php endif; ?>
                <button type="submit" class="delete-button">
                    <i class="bx bx-trash"></i>
                    <span>결과 삭제</span>
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="analysis-detail-box">
            <!-- Header with Thumbnail -->
            <div class="analysis-header-wrapper">
                <!-- Thumbnail -->
                <div class="analysis-thumbnail-container">
                    <img src="https://img.youtube.com/vi/<?= htmlspecialchars($analysis['youtube_video_id']) ?>/maxresdefault.jpg" alt="<?= htmlspecialchars($analysis['title'] ?? 'N/A') ?>" class="analysis-thumbnail-img">
                </div>

                <div class="analysis-detail-header">
                    <div class="analysis-title-meta">
                        <h2 class="analysis-detail-title">
                            <?= htmlspecialchars($analysis['title'] ?? 'N/A') ?>
                        </h2>
                        <div class="analysis-meta">
                            <i class="bx bxl-youtube"></i>
                            <span class="analysis-meta-label">Video ID:</span>
                            <a href="https://youtube.com/watch?v=<?= htmlspecialchars($analysis['youtube_video_id']) ?>" target="_blank">
                                <?= htmlspecialchars($analysis['youtube_video_id']) ?>
                                <i class="bx bx-up-arrow-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="score-box <?php
                        if($analysis['score'] >= 70) {
                            echo 'safe';
                        } elseif ($analysis['score'] >= 30) {
                            echo 'neutral';
                        } else {
                            echo 'warning';
                        }
                    ?>">
                        <div class="score-label">Score</div>
                        <div class="score-value"><?= htmlspecialchars($analysis['score']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-label">
                        <i class="bx bx-user"></i>
                        <span>Channel</span>
                    </div>
                    <div class="info-card-value" title="<?= htmlspecialchars($analysis['channel_name'] ?? 'N/A') ?>">
                        <?= htmlspecialchars($analysis['channel_name'] ?? 'N/A') ?>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-label">
                        <i class="bx bx-time"></i>
                        <span>Duration</span>
                    </div>
                    <div class="info-card-value">
                        <?= $analysis['duration'] ? gmdate("H:i:s", $analysis['duration']) : 'N/A' ?>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-label">
                        <i class="bx bx-calendar"></i>
                        <span>Published</span>
                    </div>
                    <div class="info-card-value">
                        <?= htmlspecialchars(date('Y-m-d', strtotime($analysis['published_at'] ?? 'now'))) ?>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-label">
                        <i class="bx bx-check-circle"></i>
                        <span>Analyzed</span>
                    </div>
                    <div class="info-card-value">
                        <?= htmlspecialchars(date('Y-m-d', strtotime($analysis['created_at']))) ?>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="analysis-description">
                <h3 class="section-title">
                    <i class="bx bx-list-ol"></i>
                    AI 분석 결과
                </h3>
                <div class="description-box">
                    <?= $analysis['description'] ?>
                </div>
            </div>

            <!-- Tags -->
            <?php if(!empty($analysis['tags'])): ?>
            <div>
                <h3 class="section-title">
                    <i class="bx bx-tag"></i>
                    키워드 (태그)
                </h3>
                <div class="tags-container">
                    <?php foreach(explode(',', $analysis['tags']) as $tag): ?>
                        <span class="tag">
                            <?= htmlspecialchars(trim($tag)) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Tasks Sidebar -->
    <div class="tasks-sidebar">
        <div class="tasks-header">
            <h3 class="tasks-header-title">
                <i class="bx bx-task"></i>
                수행 작업 ID
            </h3>
        </div>
            <div>
                <?php foreach ($tasks as $task): ?>
                <div class="task-item">
                    <div class="task-header">
                        <span class="task-id" title="<?= htmlspecialchars($task['task_id']) ?>">
                            <?= htmlspecialchars($task['task_id']) ?>
                        </span>
                        <span class="task-status <?php
                            echo match($task['status']) {
                                'success' => 'success',
                                'error', 'toolong' => 'error',
                                default => 'default'
                            };
                        ?>">
                            <?= htmlspecialchars($task['status']) ?>
                        </span>
                    </div>
                    <p class="task-time">
                        <i class="bx bx-time"></i>
                        <?= htmlspecialchars($task['created_at']) ?>
                    </p>
                    
                    <?php if($task['error_message']): ?>
                    <div class="task-error">
                        <div class="task-error-message">
                            <i class="bx bx-error task-error-icon"></i>
                            <span class="task-error-text"><?= htmlspecialchars($task['error_message']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($tasks)): ?>
                <div class="tasks-empty">
                    <i class="bx bx-inbox tasks-empty-icon"></i>
                    <p class="tasks-empty-text">No tasks found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Current Video Vector Snapshot -->
        <?php if(!empty($currentEmbedding)): ?>
        <div class="tasks-sidebar" style="margin-top: 1rem;">
            <div class="tasks-header">
                <h3 class="tasks-header-title">
                    <i class="bx bx-math"></i>
                    영상 정보 벡터 (임베딩)
                </h3>
            </div>
            <div style="padding: 0.8rem; font-family: monospace; font-size: 0.75rem; background: var(--bg-1); border-radius: 4px; word-break: break-all; color: var(--text-2);">
                <?= htmlspecialchars(substr($currentEmbedding, 0, 500)) ?>...
            </div>
        </div>
        <?php endif; ?>

        <!-- Related Videos Sidebar (Vector DB) -->
        <div class="tasks-sidebar" style="margin-top: 1rem;">
            <div class="tasks-header">
                <h3 class="tasks-header-title">
                    <i class="bx bx-video"></i>
                    유사 비디오 (<?= count($relatedVideos) ?>개)
                </h3>
            </div>
            <div>
                <?php if (!empty($relatedVideos)): ?>
                    <?php if (isset($relatedVideos[0]['error'])): ?>
                        <div class="task-error" style="color:var(--danger); word-break: break-all; padding: 1rem;">
                            <?= htmlspecialchars($relatedVideos[0]['error']) ?>
                        </div>
                    <?php else: ?>
                    <?php foreach ($relatedVideos as $relVideo): ?>
                    <a href="/analysis/detail?id=<?= htmlspecialchars($relVideo['id']) ?>" class="task-item" style="display: block; text-decoration: none; color: inherit;">
                        <div style="display: flex; gap: 0.8rem; align-items: start;">
                            <img src="https://img.youtube.com/vi/<?= htmlspecialchars($relVideo['youtube_video_id']) ?>/mqdefault.jpg" alt="Thumbnail" style="width: 100px; border-radius: 4px; object-fit: cover;">
                            <div style="flex: 1; overflow: hidden;">
                                <div style="font-size: 0.9rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.3rem;">
                                    <?= htmlspecialchars($relVideo['title'] ?? 'N/A') ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-2);">
                                    <?= htmlspecialchars($relVideo['channel_name'] ?? 'N/A') ?>
                                </div>
                                <div class="task-time" style="margin-top: 0.3rem;">
                                    <span class="score-box <?= $relVideo['score'] >= 70 ? 'safe' : ($relVideo['score'] >= 30 ? 'neutral' : 'warning') ?>" style="padding: 0.2rem 0.5rem; font-size: 0.7rem; display: inline-block;">
                                        Score: <?= htmlspecialchars($relVideo['score']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="tasks-empty">
                        <i class="bx bx-video-off tasks-empty-icon"></i>
                        <p class="tasks-empty-text">No related videos found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>