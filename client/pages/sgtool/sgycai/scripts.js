// 通用交互脚本

// 页面加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    
    // 评测答题页面的滑动功能
    if (document.querySelector('.question-swiper')) {
        initQuestionSwiper();
    }
    
    // 评测结果页面的标签切换功能
    if (document.querySelector('.result-tabs')) {
        initResultTabs();
    }
    
    // 分享按钮功能
    if (document.querySelector('.share-btn')) {
        initShareButtons();
    }
    
    // 分享图标点击事件
    if (document.querySelector('.share-icon')) {
        document.querySelector('.share-icon').addEventListener('click', function() {
            window.location.href = 'share.html';
        });
    }
});

// 初始化问题滑动功能
function initQuestionSwiper() {
    const nextButtons = document.querySelectorAll('.next-btn');
    const prevButtons = document.querySelectorAll('.prev-btn');
    const slides = document.querySelectorAll('.question-slide');
    
    let currentSlide = 0;
    
    // 下一题按钮点击事件
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 如果是最后一题，可以跳转到结果页或显示维度切换提示
            if (currentSlide === slides.length - 1) {
                // 这里可以添加跳转到下一个维度或结果页的逻辑
                const dimensionTransition = document.querySelector('.dimension-transition');
                if (dimensionTransition) {
                    dimensionTransition.style.display = 'flex';
                } else {
                    window.location.href = 'result.html';
                }
            } else {
                // 隐藏当前题目，显示下一题
                slides[currentSlide].classList.remove('active');
                currentSlide++;
                slides[currentSlide].classList.add('active');
                
                // 更新进度条
                updateProgressBar(currentSlide);
            }
        });
    });
    
    // 上一题按钮点击事件
    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (currentSlide > 0) {
                slides[currentSlide].classList.remove('active');
                currentSlide--;
                slides[currentSlide].classList.add('active');
                
                // 更新进度条
                updateProgressBar(currentSlide);
            }
        });
    });
    
    // 维度切换提示的继续按钮
    const continueBtn = document.querySelector('.continue-btn');
    if (continueBtn) {
        continueBtn.addEventListener('click', () => {
            document.querySelector('.dimension-transition').style.display = 'none';
            // 这里可以添加跳转到下一个维度的逻辑
            // 例如更新维度图标、标题和描述
            
            // 重置当前题目索引
            currentSlide = 0;
            slides.forEach(slide => slide.classList.remove('active'));
            slides[currentSlide].classList.add('active');
            
            // 更新进度条（假设第二部分从第8题开始）
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = '26.7%'; // 8/30
                document.querySelector('.progress-text').textContent = '8/30题';
            }
            
            // 更新维度信息
            const dimensionIcon = document.querySelector('.dimension-icon img');
            if (dimensionIcon) {
                dimensionIcon.src = 'https://img.icons8.com/fluency/96/healthy-food.png';
                document.querySelector('.dimension-text h2').textContent = '第二部分：营养状况 (2/4)';
                document.querySelector('.dimension-description p').textContent = '良好的营养是儿童身高发育的重要保障。均衡的饮食结构、充足的营养摄入对宝宝的骨骼发育和身高增长至关重要。';
            }
        });
    }
}

// 更新进度条
function updateProgressBar(currentSlide) {
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    
    if (progressBar && progressText) {
        // 假设从第3题开始
        const questionNumber = currentSlide + 3;
        const progress = (questionNumber / 30) * 100;
        
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `${questionNumber}/30题`;
    }
}

// 初始化结果页标签切换功能
function initResultTabs() {
    const tabs = document.querySelectorAll('.tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // 移除所有标签的active类
            tabs.forEach(t => t.classList.remove('active'));
            // 为当前点击的标签添加active类
            tab.classList.add('active');
            
            // 隐藏所有内容面板
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // 显示对应的内容面板
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(`${tabId}-content`).classList.add('active');
        });
    });
}

// 初始化分享按钮功能
function initShareButtons() {
    const shareButtons = document.querySelectorAll('.share-btn');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 这里可以添加实际的分享逻辑
            alert('分享功能已触发，实际应用中将调用相应的分享API');
        });
    });
} 