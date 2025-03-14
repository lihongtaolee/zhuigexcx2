<template>
  <view class="assessment-page">
    <view class="share-icon" @tap="goToShare">
      <image src="https://img.icons8.com/fluency/48/share-rounded.png" mode="aspectFit"></image>
    </view>
    
    <view class="question-container">
      <!-- 上部分 -->
      <view class="assessment-header">
        <view class="progress-section">
          <view class="dimension-info">
            <view class="dimension-icon">
              <image :src="currentDimension.icon" mode="aspectFit"></image>
            </view>
            <view class="dimension-text">
              <text class="dimension-title">{{ currentDimension.title }}</text>
            </view>
          </view>
          
          <view class="progress-bar-container">
            <view class="progress-bar" :style="{ width: progressPercent + '%' }"></view>
            <view class="progress-text">{{ currentQuestionIndex + 1 }}/{{ totalQuestions }}题</view>
          </view>
        </view>
        
        <view class="dimension-description">
          <text>{{ currentDimension.description }}</text>
        </view>
      </view>
      
      <!-- 下部分 -->
      <view class="question-section">
        <swiper class="question-swiper" :current="swiperCurrent" @change="handleSwiperChange" :disable-touch="true">
          <swiper-item v-for="(question, index) in questions" :key="index">
            <view class="question-content">
              <text class="question-title">{{ question.title }}</text>
              <view class="options-list">
                <view 
                  class="option" 
                  v-for="(option, optIndex) in question.options" 
                  :key="optIndex"
                  :class="{ 'option-selected': selectedOptions[index] === optIndex }"
                  @tap="selectOption(index, optIndex)"
                >
                  <view class="radio-circle" :class="{ 'radio-selected': selectedOptions[index] === optIndex }"></view>
                  <text class="option-text">{{ option }}</text>
                </view>
              </view>
            </view>
            <view class="navigation-buttons">
              <button class="prev-btn" @tap="prevQuestion" v-if="index > 0">上一题</button>
              <button class="next-btn" @tap="nextQuestion">{{ index === questions.length - 1 ? '完成' : '下一题' }}</button>
            </view>
          </swiper-item>
        </swiper>
      </view>
    </view>
    
    <!-- 维度切换提示 -->
    <view class="dimension-transition" v-if="showDimensionTransition">
      <view class="transition-content">
        <view class="dimension-icon large">
          <image :src="nextDimension.icon" mode="aspectFit"></image>
        </view>
        <text class="transition-title">{{ nextDimension.title }}</text>
        <text class="transition-desc">{{ nextDimension.description }}</text>
        <button class="continue-btn" @tap="startNextDimension">开始答题</button>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  data() {
    return {
      dimensions: [
        {
          id: 'genetic',
          title: '第一部分：遗传因素 (1/4)',
          icon: 'https://img.icons8.com/color/96/dna-helix.png',
          description: '遗传因素是影响儿童身高的重要决定因素之一。父母的身高、家族遗传特征等都会对孩子的最终身高产生显著影响。通过科学评估，可以更准确地预测孩子的身高发育潜力。'
        },
        {
          id: 'nutrition',
          title: '第二部分：营养状况 (2/4)',
          icon: 'https://img.icons8.com/fluency/96/healthy-food.png',
          description: '良好的营养是儿童身高发育的重要保障。均衡的饮食结构、充足的营养摄入对宝宝的骨骼发育和身高增长至关重要。'
        },
        {
          id: 'exercise',
          title: '第三部分：运动习惯 (3/4)',
          icon: 'https://img.icons8.com/color/96/running.png',
          description: '适当的运动可以促进骨骼发育和生长激素分泌，对儿童身高发育有积极影响。我们将评估宝宝的运动习惯是否有利于身高增长。'
        },
        {
          id: 'sleep',
          title: '第四部分：睡眠质量 (4/4)',
          icon: 'https://img.icons8.com/color/96/sleeping.png',
          description: '充足的睡眠对生长激素的分泌至关重要。我们将评估宝宝的睡眠时间和质量是否满足身高发育的需要。'
        }
      ],
      currentDimensionIndex: 0,
      questions: [
        {
          title: '问题3：父亲的身高是多少？',
          options: ['165cm以下', '165-170cm', '170-175cm', '175-180cm', '180cm以上']
        },
        {
          title: '问题4：母亲的身高是多少？',
          options: ['155cm以下', '155-160cm', '160-165cm', '165-170cm', '170cm以上']
        },
        // 可以添加更多问题
      ],
      selectedOptions: {},
      swiperCurrent: 0,
      showDimensionTransition: false,
      totalQuestions: 30 // 总题目数量
    }
  },
  computed: {
    currentDimension() {
      return this.dimensions[this.currentDimensionIndex];
    },
    nextDimension() {
      return this.currentDimensionIndex < this.dimensions.length - 1 
        ? this.dimensions[this.currentDimensionIndex + 1] 
        : null;
    },
    currentQuestionIndex() {
      // 根据当前维度和swiper位置计算题目索引
      let baseIndex = 0;
      for (let i = 0; i < this.currentDimensionIndex; i++) {
        baseIndex += 7; // 假设每个维度有7个问题
      }
      return baseIndex + this.swiperCurrent;
    },
    progressPercent() {
      return ((this.currentQuestionIndex + 1) / this.totalQuestions) * 100;
    }
  },
  methods: {
    handleSwiperChange(e) {
      this.swiperCurrent = e.detail.current;
    },
    selectOption(questionIndex, optionIndex) {
      this.$set(this.selectedOptions, questionIndex, optionIndex);
    },
    prevQuestion() {
      if (this.swiperCurrent > 0) {
        this.swiperCurrent--;
      }
    },
    nextQuestion() {
      if (this.swiperCurrent < this.questions.length - 1) {
        this.swiperCurrent++;
      } else {
        // 如果是当前维度的最后一题
        if (this.currentDimensionIndex < this.dimensions.length - 1) {
          // 还有下一个维度，显示维度切换提示
          this.showDimensionTransition = true;
        } else {
          // 已经是最后一个维度，跳转到结果页
          this.goToResult();
        }
      }
    },
    startNextDimension() {
      this.showDimensionTransition = false;
      this.currentDimensionIndex++;
      this.swiperCurrent = 0;
      // 这里可以加载下一个维度的问题
    },
    goToShare() {
      uni.navigateTo({
        url: './share'
      });
    },
    goToResult() {
      // 保存答题结果
      uni.setStorageSync('assessmentResults', this.selectedOptions);
      
      // 跳转到结果页
      uni.navigateTo({
        url: './result'
      });
    }
  }
}
</script>

<style>
.assessment-page {
  background-color: #fff;
  min-height: 100vh;
  position: relative;
}

.share-icon {
  position: fixed;
  top: 40rpx;
  right: 40rpx;
  z-index: 100;
  background-color: #fff;
  border-radius: 50%;
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.1);
}

.share-icon image {
  width: 40rpx;
  height: 40rpx;
}

.question-container {
  padding: 30rpx;
  padding-top: 80rpx;
  max-width: 750rpx;
  margin: 0 auto;
}

.assessment-header {
  margin-bottom: 40rpx;
}

.progress-section {
  margin-bottom: 30rpx;
}

.dimension-info {
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
}

.dimension-icon {
  margin-right: 20rpx;
}

.dimension-icon image {
  width: 80rpx;
  height: 80rpx;
}

.dimension-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #ff6b81;
}

.progress-bar-container {
  background-color: #f0f0f0;
  border-radius: 40rpx;
  height: 16rpx;
  overflow: hidden;
  position: relative;
  margin-bottom: 10rpx;
}

.progress-bar {
  background-color: #ff6b81;
  height: 100%;
  border-radius: 40rpx;
  transition: width 0.3s ease;
}

.progress-text {
  text-align: right;
  font-size: 24rpx;
  color: #999;
}

.dimension-description {
  background-color: #F8F8F8;
  border-radius: 16rpx;
  padding: 20rpx;
  font-size: 26rpx;
  color: #666;
}

.question-section {
  background-color: #fff;
  border-radius: 20rpx;
  padding: 30rpx;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.05);
}

.question-swiper {
  height: 800rpx;
  margin-bottom: 40rpx;
}

.question-content {
  padding: 20rpx 0;
  min-height: 600rpx;
}

.question-title {
  font-size: 34rpx;
  font-weight: bold;
  margin-bottom: 40rpx;
  display: block;
}

.options-list {
  margin-bottom: 40rpx;
}

.option {
  display: flex;
  align-items: center;
  background-color: #F8F8F8;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 20rpx;
  transition: background-color 0.3s ease;
  width: 100%;
}

.option:hover {
  background-color: #f0f0f0;
}

.option-selected {
  background-color: rgba(255, 107, 129, 0.1);
  border: 2rpx solid #ff6b81;
}

.radio-circle {
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  border: 2rpx solid #ccc;
  margin-right: 20rpx;
  position: relative;
}

.radio-selected {
  border-color: #ff6b81;
}

.radio-selected::after {
  content: '';
  position: absolute;
  width: 24rpx;
  height: 24rpx;
  background-color: #ff6b81;
  border-radius: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.option-text {
  font-size: 30rpx;
}

.navigation-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 40rpx;
  padding-bottom: 60rpx;
}

.prev-btn, .next-btn {
  background-color: #ff6b81;
  color: white;
  font-size: 30rpx;
  padding: 16rpx 40rpx;
  border-radius: 40rpx;
  min-width: 180rpx;
  text-align: center;
}

.prev-btn:hover, .next-btn:hover {
  background-color: #ff5268;
}

.dimension-transition {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.transition-content {
  text-align: center;
  padding: 40rpx;
  width: 80%;
  max-width: 600rpx;
}

.dimension-icon.large image {
  width: 160rpx;
  height: 160rpx;
  margin-bottom: 30rpx;
}

.transition-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-bottom: 20rpx;
  display: block;
}

.transition-desc {
  font-size: 28rpx;
  color: #666;
  margin-bottom: 40rpx;
  display: block;
}

.continue-btn {
  background-color: #ff6b81;
  color: white;
  font-size: 30rpx;
  padding: 16rpx 40rpx;
  border-radius: 40rpx;
  margin-top: 30rpx;
}

.continue-btn:hover {
  background-color: #ff5268;
}
</style> 