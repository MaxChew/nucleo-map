<template>
    <div class="users-list">
        <h2 class="text-2xl font-bold mb-4">用户管理</h2>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <p>用户列表内容将在这里显示</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UsersList',
    data() {
        return {
            users: []
        }
    }
}
</script> 