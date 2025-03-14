<template>
  <view class="main-page">
    <view class="container">
      <view class="main-header">
        <text class="title">儿童身高在线评测</text>
        <text class="subtitle">专业评估，科学指导，助力宝宝健康成长</text>
      </view>
      
      <view class="navigation-cards">
        <navigator url="./assessment" class="nav-card" hover-class="nav-card-hover">
          <view class="card-icon">
            <image src="https://img.icons8.com/color/96/test-partial-passed.png" mode="aspectFit"></image>
          </view>
          <text class="card-title">开始评测</text>
          <text class="card-desc">30道专业题目，全面评估宝宝身高发育状况</text>
        </navigator>
        
        <navigator url="./result" class="nav-card" hover-class="nav-card-hover">
          <view class="card-icon">
            <image src="https://img.icons8.com/color/96/combo-chart--v1.png" mode="aspectFit"></image>
          </view>
          <text class="card-title">评测结果</text>
          <text class="card-desc">查看详细的评测报告和专业建议</text>
        </navigator>
        
        <navigator url="./share" class="nav-card" hover-class="nav-card-hover">
          <view class="card-icon">
            <image src="https://img.icons8.com/color/96/share-rounded.png" mode="aspectFit"></image>
          </view>
          <text class="card-title">邀请分享</text>
          <text class="card-desc">与亲友分享评测，共同关注宝宝健康成长</text>
        </navigator>
      </view>
      
      <view class="intro-section">
        <text class="section-title">关于儿童身高评测</text>
        <text class="section-desc">我们的评测系统基于专业医学研究，从四大维度全面评估宝宝的身高发育状况，为您提供科学的成长指导。</text>
        
        <view class="dimension-cards">
          <view class="dimension-card">
            <text class="dimension-title">遗传因素</text>
            <text class="dimension-desc">评估父母身高、家族遗传等因素对宝宝身高的影响</text>
          </view>
          <view class="dimension-card">
            <text class="dimension-title">营养状况</text>
            <text class="dimension-desc">分析宝宝的饮食结构、营养摄入是否均衡充足</text>
          </view>
          <view class="dimension-card">
            <text class="dimension-title">运动习惯</text>
            <text class="dimension-desc">评估宝宝的运动频率、强度和类型是否有利于身高发育</text>
          </view>
          <view class="dimension-card">
            <text class="dimension-title">睡眠质量</text>
            <text class="dimension-desc">分析宝宝的睡眠时长、质量对生长激素分泌的影响</text>
          </view>
        </view>
      </view>
    </view>
    
    <view class="main-footer">
      <text>儿童身高在线评测 © 2023</text>
    </view>
  </view>
</template>

<script>
export default {
  data() {
    return {}
  },
  onLoad() {
    // 页面加载时执行
  },
  methods: {
    // 导航卡片点击效果
    cardHover(e) {
      // 在小程序中，hover效果通过CSS的hover-class实现
      // 这里可以添加额外的交互逻辑
    }
  }
}
</script>

<style>
.main-page {
  background-color: #fff;
  min-height: 100vh;
}

.container {
  padding: 30rpx;
  max-width: 750rpx;
  margin: 0 auto;
}

.main-header {
  text-align: center;
  padding: 40rpx 0;
  width: 100%;
}

.title {
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b81;
  display: block;
  margin-bottom: 20rpx;
  text-align: center;
}

.subtitle {
  font-size: 28rpx;
  color: #777;
  display: block;
  text-align: center;
}

.navigation-cards {
  margin: 40rpx 0;
  width: 100%;
}

.nav-card {
  background-color: #fff;
  border-radius: 20rpx;
  padding: 40rpx;
  margin-bottom: 30rpx;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.3s ease;
  width: 100%;
  box-sizing: border-box;
}

.nav-card-hover {
  transform: translateY(-6rpx);
}

.card-icon {
  margin-bottom: 20rpx;
}

.card-icon image {
  width: 120rpx;
  height: 120rpx;
}

.card-title {
  font-size: 34rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-bottom: 16rpx;
  display: block;
}

.card-desc {
  font-size: 28rpx;
  color: #666;
  text-align: center;
  display: block;
}

.intro-section {
  background-color: #f9f9f9;
  border-radius: 20rpx;
  padding: 40rpx;
  margin: 40rpx 0;
  width: 100%;
  box-sizing: border-box;
}

.section-title {
  font-size: 34rpx;
  font-weight: bold;
  color: #ff6b81;
  text-align: center;
  margin-bottom: 30rpx;
  display: block;
}

.section-desc {
  font-size: 28rpx;
  color: #666;
  margin-bottom: 30rpx;
  display: block;
}

.dimension-cards {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
  width: 100%;
}

.dimension-card {
  background-color: #fff;
  border-radius: 16rpx;
  padding: 30rpx;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.03);
  width: 100%;
  box-sizing: border-box;
}

.dimension-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-bottom: 10rpx;
  display: block;
}

.dimension-desc {
  font-size: 26rpx;
  color: #666;
  display: block;
}

.main-footer {
  text-align: center;
  padding: 30rpx;
  color: #999;
  font-size: 24rpx;
}

/* 添加响应式样式，确保在不同设备上显示一致 */
@media screen and (min-width: 768rpx) {
  .dimension-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20rpx;
  }
}
</style> 