<div class="content">
    <h2 class="title is-4">환경 변수</h2>

    <!-- Env List -->
    <div class="box mt-5">
        <h3 class="title is-5 mb-4"><i class="bx bx-cog"></i> 환경 변수 목록</h3>
        <p class="help is-danger mb-4"><i class="bx bx-error"></i> 주의: 환경 변수를 잘못 변경하면 애플리케이션 작동이 멈출 수 있습니다.</p>
        <div class="table-container">
            <table class="table is-fullwidth is-hoverable">
                <thead>
                    <tr>
                        <th style="width: 30%">키 (KEY)</th>
                        <th style="width: 50%">값 (VALUE)</th>
                        <th style="width: 20%">동작</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($envVars)): ?>
                        <?php foreach ($envVars as $key => $value): ?>
                            <tr>
                                <td><span class="tag is-info has-text-weight-bold"><?= htmlspecialchars($key) ?></span></td>
                                <td>
                                    <!-- 수정 폼 -->
                                    <form action="/settings/env/store" method="POST" style="display:flex; width:100%; gap: 0.5rem;">
                                        <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                        <input type="text" name="value" class="input is-small" value="<?= htmlspecialchars($value) ?>" required>
                                        <button type="submit" class="button is-small is-primary">저장</button>
                                    </form>
                                </td>
                                <td>
                                    <!-- 삭제 폼 -->
                                    <form action="/settings/env/delete" method="POST" style="display:inline;" onsubmit="return confirm('[<?= htmlspecialchars($key) ?>] 키를 정말 삭제하시겠습니까? 관련 기능이 멈출 수 있습니다.');">
                                        <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                        <button type="submit" class="button is-small is-danger">
                                            <i class="bx bx-trash"></i> 삭제
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="has-text-centered text-muted py-5">
                                <i class="bx bx-info-circle is-size-4 mb-2"></i><br>
                                등록된 환경 변수가 없거나, .env 파일에 접근할 수 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Env Form -->
    <div class="box mt-5">
        <h3 class="title is-5 mb-4"><i class="bx bx-plus-circle"></i> 새 환경 변수 추가</h3>
        <form action="/settings/env/store" method="POST">
            <div class="columns is-multiline">
                <div class="column is-5">
                    <div class="field">
                        <label class="label">키 (KEY)</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="key" placeholder="DB_HOST" pattern="^[A-Z0-9_]+$" title="알파벳 대문자, 숫자, 언더바(_)만 가능합니다." required>
                            <span class="icon is-small is-left">
                                <i class="bx bx-key"></i>
                            </span>
                        </div>
                        <p class="help">알파벳 대문자, 숫자, 언더바(_)만 사용 가능</p>
                    </div>
                </div>
                <div class="column is-5">
                    <div class="field">
                        <label class="label">값 (VALUE)</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="value" placeholder="127.0.0.1" required>
                            <span class="icon is-small is-left">
                                <i class="bx bx-data"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field w-100">
                        <label class="label">&nbsp;</label>
                        <button type="submit" class="button is-primary is-fullwidth">
                            추가
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>