<template>
  <view class="share-page">
    <view class="share-container">
      <view class="share-header">
        <text class="share-title">邀请好友一起评测</text>
        <text class="share-subtitle">关爱宝宝成长，从科学评测开始</text>
      </view>
      
      <view class="share-card">
        <view class="share-image">
          <image src="https://img.freepik.com/free-vector/hand-drawn-family-with-baby_23-2149153282.jpg" mode="aspectFit"></image>
        </view>
        <view class="share-text">
          <text class="card-title">儿童身高在线评测</text>
          <text class="card-desc">专业四维评估，科学指导宝宝健康成长</text>
          <view class="share-features">
            <view class="feature-tag">遗传因素分析</view>
            <view class="feature-tag">营养状况评估</view>
            <view class="feature-tag">运动习惯指导</view>
            <view class="feature-tag">睡眠质量建议</view>
          </view>
        </view>
      </view>
      
      <view class="share-benefits">
        <text class="section-title">为什么要分享给好友？</text>
        <view class="benefits-list">
          <view class="benefit-item">
            <view class="benefit-icon">
              <image src="https://img.icons8.com/color/96/microscope.png" mode="aspectFit"></image>
            </view>
            <view class="benefit-text">
              <text class="benefit-title">科学评测</text>
              <text class="benefit-desc">基于专业医学研究，提供科学的身高发育评估</text>
            </view>
          </view>
          <view class="benefit-item">
            <view class="benefit-icon">
              <image src="https://img.icons8.com/color/96/idea.png" mode="aspectFit"></image>
            </view>
            <view class="benefit-text">
              <text class="benefit-title">个性化建议</text>
              <text class="benefit-desc">根据评测结果，获取针对性的成长建议</text>
            </view>
          </view>
          <view class="benefit-item">
            <view class="benefit-icon">
              <image src="https://img.icons8.com/color/96/group.png" mode="aspectFit"></image>
            </view>
            <view class="benefit-text">
              <text class="benefit-title">共同关注</text>
              <text class="benefit-desc">与亲友一起关注宝宝健康成长，交流育儿经验</text>
            </view>
          </view>
        </view>
      </view>
      
      <view class="share-actions">
        <button class="share-btn primary" @tap="shareToWechat">
          <image src="https://img.icons8.com/color/48/weixing.png" mode="aspectFit"></image>
          <text>分享到微信</text>
        </button>
        <button class="share-btn" @tap="shareToTimeline">
          <image src="https://img.icons8.com/fluency/48/instagram-new.png" mode="aspectFit"></image>
          <text>分享到朋友圈</text>
        </button>
        <button class="share-btn" @tap="copyLink">
          <image src="https://img.icons8.com/color/48/link--v1.png" mode="aspectFit"></image>
          <text>复制链接</text>
        </button>
      </view>
      
      <view class="share-qrcode">
        <text class="qrcode-text">扫描二维码，立即体验</text>
        <image src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://example.com/sgtool/sgycai" mode="aspectFit" class="qrcode-image"></image>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  data() {
    return {
      shareLink: 'https://example.com/sgtool/sgycai',
      shareTitle: '儿童身高在线评测 - 科学指导宝宝健康成长',
      shareDesc: '专业四维评估，助力宝宝身高发育，快来测一测吧！',
      shareImgUrl: 'https://img.freepik.com/free-vector/hand-drawn-family-with-baby_23-2149153282.jpg'
    }
  },
  onLoad() {
    // 页面加载时执行
  },
  methods: {
    shareToWechat() {
      // 分享到微信好友
      uni.share({
        provider: 'weixin',
        scene: 'WXSceneSession',
        type: 0,
        title: this.shareTitle,
        summary: this.shareDesc,
        imageUrl: this.shareImgUrl,
        href: this.shareLink,
        success: (res) => {
          console.log('分享成功：', res);
          uni.showToast({
            title: '分享成功',
            icon: 'success'
          });
        },
        fail: (err) => {
          console.log('分享失败：', err);
          uni.showToast({
            title: '分享失败',
            icon: 'none'
          });
        }
      });
    },
    shareToTimeline() {
      // 分享到朋友圈
      uni.share({
        provider: 'weixin',
        scene: 'WXSenceTimeline',
        type: 0,
        title: this.shareTitle,
        summary: this.shareDesc,
        imageUrl: this.shareImgUrl,
        href: this.shareLink,
        success: (res) => {
          console.log('分享成功：', res);
          uni.showToast({
            title: '分享成功',
            icon: 'success'
          });
        },
        fail: (err) => {
          console.log('分享失败：', err);
          uni.showToast({
            title: '分享失败',
            icon: 'none'
          });
        }
      });
    },
    copyLink() {
      // 复制链接到剪贴板
      uni.setClipboardData({
        data: this.shareLink,
        success: () => {
          uni.showToast({
            title: '链接已复制',
            icon: 'success'
          });
        }
      });
    }
  }
}
</script>

<style>
.share-page {
  background-color: #fff;
  min-height: 100vh;
}

.share-container {
  padding: 30rpx;
  max-width: 750rpx;
  margin: 0 auto;
}

.share-header {
  text-align: center;
  padding: 40rpx 0;
}

.share-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b81;
  display: block;
  margin-bottom: 20rpx;
}

.share-subtitle {
  font-size: 28rpx;
  color: #666;
  display: block;
}

.share-card {
  background-color: #F8F8F8;
  border-radius: 20rpx;
  padding: 40rpx;
  margin-bottom: 40rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  box-sizing: border-box;
}

.share-image {
  margin-bottom: 30rpx;
  width: 100%;
  text-align: center;
}

.share-image image {
  width: 400rpx;
  height: 300rpx;
  border-radius: 16rpx;
  object-fit: cover;
}

.share-text {
  text-align: center;
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
  margin-bottom: 20rpx;
  display: block;
}

.share-features {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20rpx;
  margin-top: 20rpx;
  width: 100%;
}

.feature-tag {
  background-color: #fff;
  border-radius: 30rpx;
  padding: 10rpx 20rpx;
  font-size: 24rpx;
  color: #ff6b81;
  border: 2rpx solid #ff6b81;
  width: calc(50% - 30rpx);
  text-align: center;
  box-sizing: border-box;
}

.share-benefits {
  margin-bottom: 40rpx;
}

.section-title {
  font-size: 34rpx;
  font-weight: bold;
  color: #ff6b81;
  text-align: center;
  margin-bottom: 30rpx;
  display: block;
}

.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
}

.benefit-item {
  background-color: #F8F8F8;
  border-radius: 16rpx;
  padding: 30rpx;
  display: flex;
  align-items: center;
}

.benefit-icon {
  margin-right: 20rpx;
}

.benefit-icon image {
  width: 80rpx;
  height: 80rpx;
}

.benefit-text {
  flex: 1;
}

.benefit-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-bottom: 10rpx;
  display: block;
}

.benefit-desc {
  font-size: 26rpx;
  color: #666;
  display: block;
}

.share-actions {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
  margin-bottom: 40rpx;
}

.share-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #F8F8F8;
  border-radius: 16rpx;
  padding: 30rpx;
  font-size: 30rpx;
  color: #333;
}

.share-btn image {
  width: 40rpx;
  height: 40rpx;
  margin-right: 20rpx;
}

.share-btn.primary {
  background-color: #ff6b81;
  color: white;
}

.share-qrcode {
  text-align: center;
  margin-top: 40rpx;
}

.qrcode-text {
  font-size: 28rpx;
  color: #666;
  margin-bottom: 20rpx;
  display: block;
}

.qrcode-image {
  width: 300rpx;
  height: 300rpx;
  margin: 0 auto;
}
</style> 