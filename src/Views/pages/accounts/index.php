<div class="content">
    <h2 class="title is-4">계정 관리</h2>

    <!-- Administrator List -->
    <div class="box mt-5">
        <h3 class="title is-5 mb-4"><i class="bx bx-list-ul"></i> 관리자 목록</h3>
        <div class="table-container">
            <table class="table is-fullwidth is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>유저네임</th>
                        <th>이메일</th>
                        <th>권한</th>
                        <th>동작</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accounts)): ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?= htmlspecialchars($account['id']) ?></td>
                                <td><?= htmlspecialchars($account['username']) ?></td>
                                <td><?= htmlspecialchars($account['email']) ?></td>
                                <td>
                                    <span class="tag <?= $account['role'] === 'Super Admin' ? 'is-primary' : 'is-warning' ?>">
                                        <?= htmlspecialchars($account['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Delete form -->
                                    <form action="/accounts/delete" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($account['id']) ?>">
                                        <?php 
                                            $canDelete = isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $account['id'] || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Super Admin'));
                                        ?>
                                        <button type="submit" class="button is-small is-danger" <?= !$canDelete ? 'disabled' : '' ?>>
                                            <i class="bx bx-trash"></i> 삭제
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="has-text-centered text-muted py-5">
                                <i class="bx bx-info-circle is-size-4 mb-2"></i><br>
                                등록된 계정이 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Account Form -->
    <div class="box mt-5">
        <h3 class="title is-5 mb-4"><i class="bx bx-user-plus"></i> 새 계정 추가</h3>
        <form action="/accounts/store" method="POST">
            <div class="columns is-multiline">
                <div class="column is-6">
                    <div class="field">
                        <label class="label">유저네임</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="username" placeholder="username" required>
                            <span class="icon is-small is-left">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">이메일</label>
                        <div class="control has-icons-left">
                            <input class="input" type="email" name="email" placeholder="email@example.com" required>
                            <span class="icon is-small is-left">
                                <i class="bx bx-envelope"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">비밀번호</label>
                        <div class="control has-icons-left">
                            <input class="input" type="password" name="password" placeholder="********" required minlength="6">
                            <span class="icon is-small is-left">
                                <i class="bx bx-lock-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        <label class="label">권한</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="role">
                                    <option value="Manager">Manager</option>
                                    <option value="Super Admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-12">
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" type="submit">
                                <i class="bx bx-plus mr-1"></i> 계정 추가
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
