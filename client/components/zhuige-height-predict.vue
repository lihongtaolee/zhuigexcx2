<template>
  <view class="height-predict-container">
    <!-- 模块头部 -->
    <view class="module-header">
      <view class="title-wrapper">
        <text class="module-title">身高预测AI</text>
        <text v-if="updateTime" class="update-time">{{ updateTime }}</text>
      </view>
      <text class="baobao-name">{{ baobaoname }}</text>
    </view>

    <!-- 数据展示区域 - 使用简单直接的结构 -->
    <view class="data-section">
      <view class="data-item">
        <image class="icon" src="/static/sgtoolimages/astronaut.png" mode="aspectFit"></image>
        <text class="value">{{ geneticHeight }}<text class="unit">cm</text></text>
        <text class="label">遗传身高</text>
      </view>
      <view class="data-item">
        <image class="icon" src="/static/sgtoolimages/astronaut.png" mode="aspectFit"></image>
        <text class="value">{{ currentHeight }}<text class="unit">cm</text></text>
        <text class="label">实测身高</text>
      </view>
      <view class="data-item">
        <image class="icon" src="/static/sgtoolimages/astronaut.png" mode="aspectFit"></image>
        <text class="value">{{ targetHeight }}<text class="unit">cm</text></text>
        <text class="label">期望身高</text>
      </view>
      <view class="data-item">
        <image class="icon" src="/static/sgtoolimages/astronaut.png" mode="aspectFit"></image>
        <text class="value probability">{{ probability }}<text class="unit">%</text></text>
        <text class="label">可追高概率</text>
      </view>
    </view>

    <!-- 详细预测入口 -->
    <view class="detail-guide-wrapper" @click="openDetail">
      <view class="detail-guide">
        <text class="guide-text">开始详细预测</text>
        <text class="guide-icon">></text>
      </view>
      <text class="guide-desc">{{ baobaoname === '演示宝宝' ? '填写宝宝身高档案，获取专属预测' : '查看宝宝详细身高预测报告' }}</text>
    </view>
  </view>
</template>

<script>
export default {
  name: 'zhuige-height-predict',
  props: {
    currentHeight: {
      type: Number,
      required: true,
      default: 0
    },
    geneticHeight: {
      type: Number,
      required: true,
      default: 0
    },
    targetHeight: {
      type: Number,
      required: true,
      default: 0
    },
    probability: {
      type: Number,
      required: true,
      default: 0
    },
    baobaoname: {
      type: String,
      required: true,
      default: '演示宝宝'
    },
    updateTime: {
      type: String,
      default: ''
    }
  },
  methods: {
    openDetail() {
      if (typeof uni !== 'undefined') {
        uni.navigateTo({ url: '/pages/sgtool/sgycai/sgycai' });
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.height-predict-container {
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e4ff 100%);
  border-radius: 16rpx;
  padding: 20rpx;
  margin: 0;
  box-shadow: 0 4rpx 12rpx rgba(106, 90, 205, 0.1);
  position: relative;
  overflow: visible;
}

.module-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 15rpx;

  .title-wrapper {
    display: flex;
    align-items: center;
    gap: 10rpx;
  }
  
  .module-title {
    font-size: 32rpx;
    color: #333;
    font-weight: 700;
  }

  .update-time {
    font-size: 24rpx;
    color: #999;
    margin-left: 10rpx;
  }

  .baobao-name {
    font-size: 24rpx;
    color: #666;
  }
}

.data-section {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20rpx;
  
  .data-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 6rpx;
    
    .icon {
      width: 48rpx;
      height: 48rpx;
      margin-bottom: 8rpx;
    }
    
    .value {
      font-size: 40rpx;
      font-weight: 800;
      color: #7b68ee;
      margin-bottom: 10rpx;
      text-align: center;
      
      &.probability {
        color: #7b68ee;
      }

      .unit {
        font-size: 24rpx;
        font-weight: normal;
        margin-left: 4rpx;
      }
    }
    
    .label {
      font-size: 24rpx;
      color: #666;
      font-weight: 500;
    }
  }
}

.detail-guide-wrapper {
  background: linear-gradient(to right, rgba(123, 104, 238, 0.8), rgba(106, 90, 205, 0.8));
  border-radius: 12rpx;
  padding: 12rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;

  .detail-guide {
    display: flex;
    align-items: center;
    margin-bottom: 4rpx;

    .guide-text {
      font-size: 28rpx;
      color: #ffffff;
      font-weight: 700;
    }

    .guide-icon {
      margin-left: 6rpx;
      color: #ffffff;
      font-weight: 700;
    }
  }

  .guide-desc {
    font-size: 20rpx;
    color: rgba(255, 255, 255, 0.9);
  }
}

.highlight {
  color: #6a5acd;
  font-weight: bold;
}
</style>