<template>
  <view class="category-container">
    <!-- 分类标题 -->
    <view class="category-header">
      <text class="category-title">{{ categoryInfo.name }}</text>
    </view>
    
    <!-- 金刚位模块 -->
    <view class="kingkong-module" v-if="subCategories.length > 0">
      <view class="kingkong-list">
        <view 
          v-for="(item, index) in subCategories" 
          :key="index" 
          class="kingkong-item"
          @click="navigateToSubCategory(item.id)"
        >
          <image class="kingkong-icon" :src="item.icon" mode="aspectFill"></image>
          <text class="kingkong-name">{{ item.name }}</text>
        </view>
      </view>
    </view>
    
    <!-- 推荐配置模块1 -->
    <view class="recommend-module" v-if="recommendProducts1.length > 0">
      <view class="module-header">
        <text class="module-title">{{ recommendTitle1 }}</text>
      </view>
      <scroll-view class="recommend-scroll" scroll-x="true" show-scrollbar="false">
        <view class="recommend-list">
          <view 
            v-for="(item, index) in recommendProducts1" 
            :key="index" 
            class="recommend-item"
            @click="navigateToDetail(item)"
          >
            <image class="recommend-image" :src="item.image" mode="aspectFill"></image>
            <view class="recommend-info">
              <text class="recommend-title">{{ item.title }}</text>
              <view class="recommend-price-row">
                <text class="recommend-price">¥{{ item.price }}</text>
                <text class="recommend-original-price">¥{{ item.original_price }}</text>
              </view>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>
    
    <!-- 推荐配置模块2 -->
    <view class="recommend-module" v-if="recommendProducts2.length > 0">
      <view class="module-header">
        <text class="module-title">{{ recommendTitle2 }}</text>
      </view>
      <scroll-view class="recommend-scroll" scroll-x="true" show-scrollbar="false">
        <view class="recommend-list">
          <view 
            v-for="(item, index) in recommendProducts2" 
            :key="index" 
            class="recommend-item"
            @click="navigateToDetail(item)"
          >
            <image class="recommend-image" :src="item.image" mode="aspectFill"></image>
            <view class="recommend-info">
              <text class="recommend-title">{{ item.title }}</text>
              <view class="recommend-price-row">
                <text class="recommend-price">¥{{ item.price }}</text>
                <text class="recommend-original-price">¥{{ item.original_price }}</text>
              </view>
            </view>
          </view>
        </view>
      </scroll-view>
    </view>
    
    <!-- 商品列表标题 -->
    <view class="product-list-header">
      <text class="product-list-title">全部商品</text>
    </view>
    
    <!-- 商品列表 -->
    <view class="product-list">
      <view 
        v-for="(item, index) in products" 
        :key="index" 
        class="product-item"
        @click="navigateToDetail(item)"
      >
        <image class="product-image" :src="item.image" mode="aspectFill"></image>
        <view class="product-info">
          <text class="product-title">{{ item.title }}</text>
          <view class="product-price-row">
            <text class="product-price">¥{{ item.price }}</text>
            <text class="product-original-price">¥{{ item.original_price }}</text>
          </view>
        </view>
      </view>
    </view>
    
    <!-- 无数据提示 -->
    <zhuige-nodata v-if="products.length === 0"></zhuige-nodata>
    
    <!-- 加载更多 -->
    <view class="loading-more" v-if="isLoading">
      <text>加载中...</text>
    </view>
  </view>
</template>

<script>
import Auth from '@/utils/auth.js';
import Util from '@/utils/util.js';
import ZhuigeNodata from '@/components/zhuige-nodata.vue';

export default {
  components: {
    ZhuigeNodata
  },
  
  data() {
    return {
      categoryId: 0,
      categoryInfo: {},
      subCategories: [],
      recommendProducts1: [],
      recommendProducts2: [],
      recommendTitle1: '热门推荐',
      recommendTitle2: '新品上市',
      products: [],
      page: 1,
      isLoading: false,
      hasMore: true
    }
  },
  
  onLoad(options) {
    if (options.id) {
      this.categoryId = parseInt(options.id);
      this.loadCategoryInfo();
      this.loadSubCategories();
      this.loadRecommendProducts();
      this.loadProducts();
    }
  },
  
  onReachBottom() {
    if (this.hasMore && !this.isLoading) {
      this.loadMoreProducts();
    }
  },
  
  onPullDownRefresh() {
    this.refreshData();
  },
  
  methods: {
    // 加载分类信息
    loadCategoryInfo() {
      uni.request({
        url: getApp().globalData.zhuige_api_url + 'sgtool/shop/category',
        method: 'GET',
        data: {
          id: this.categoryId
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            this.categoryInfo = res.data.data;
            uni.setNavigationBarTitle({
              title: this.categoryInfo.name
            });
          }
        }
      });
    },
    
    // 加载二级分类
    loadSubCategories() {
      uni.request({
        url: getApp().globalData.zhuige_api_url + 'sgtool/shop/subcategories',
        method: 'GET',
        data: {
          parent_id: this.categoryId
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            this.subCategories = res.data.data;
          }
        }
      });
    },
    
    // 加载推荐商品
    loadRecommendProducts() {
      uni.request({
        url: getApp().globalData.zhuige_api_url + 'sgtool/shop/recommend',
        method: 'GET',
        data: {
          category_id: this.categoryId
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            const data = res.data.data;
            if (data.recommend1) {
              this.recommendProducts1 = data.recommend1.products || [];
              this.recommendTitle1 = data.recommend1.title || '热门推荐';
            }
            if (data.recommend2) {
              this.recommendProducts2 = data.recommend2.products || [];
              this.recommendTitle2 = data.recommend2.title || '新品上市';
            }
          }
        }
      });
    },
    
    // 加载商品
    loadProducts() {
      this.isLoading = true;
      this.page = 1;
      this.products = [];
      
      uni.request({
        url: getApp().globalData.zhuige_api_url + 'sgtool/shop/products',
        method: 'GET',
        data: {
          category_id: this.categoryId,
          page: this.page
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = res.data.data.list;
            this.hasMore = res.data.data.more;
          } else {
            uni.showToast({
              title: res.data.msg || '加载商品失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          uni.showToast({
            title: '网络错误',
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
          uni.stopPullDownRefresh();
        }
      });
    },
    
    // 加载更多商品
    loadMoreProducts() {
      if (!this.hasMore || this.isLoading) {
        return;
      }
      
      this.isLoading = true;
      this.page++;
      
      uni.request({
        url: getApp().globalData.zhuige_api_url + 'sgtool/shop/products',
        method: 'GET',
        data: {
          category_id: this.categoryId,
          page: this.page
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = [...this.products, ...res.data.data.list];
            this.hasMore = res.data.data.more;
          } else {
            this.page--;
            uni.showToast({
              title: res.data.msg || '加载更多失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          this.page--;
          uni.showToast({
            title: '网络错误',
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
        }
      });
    },
    
    // 刷新数据
    refreshData() {
      this.loadCategoryInfo();
      this.loadSubCategories();
      this.loadRecommendProducts();
      this.loadProducts();
    },
    
    // 跳转到二级分类
    navigateToSubCategory(subCategoryId) {
      uni.navigateTo({
        url: '/pages/sgtool/shop/subcategory?id=' + subCategoryId
      });
    },
    
    // 跳转到商品详情
    navigateToDetail(product) {
      if (product.app_id && product.path) {
        // 跳转到其他小程序
        uni.navigateToMiniProgram({
          appId: product.app_id,
          path: product.path,
          success: function() {
            console.log('跳转成功');
          },
          fail: function(err) {
            console.error('跳转失败', err);
            uni.showToast({
              title: '跳转失败',
              icon: 'none'
            });
          }
        });
      }
    }
  }
}
</script>

<style scoped>
.category-container {
  min-height: 100vh;
  background-color: #f5f5f5;
  padding-bottom: 20rpx;
}

.category-header {
  background-color: #ffffff;
  padding: 30rpx 20rpx;
  border-bottom: 1rpx solid #eee;
}

.category-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
}

.kingkong-module {
  background-color: #ffffff;
  padding: 20rpx;
  margin-bottom: 20rpx;
}

.kingkong-list {
  display: flex;
  flex-wrap: wrap;
}

.kingkong-item {
  width: 20%;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx 0;
}

.kingkong-icon {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
}

.kingkong-name {
  font-size: 24rpx;
  color: #333;
  margin-top: 10rpx;
  text-align: center;
}

.recommend-module {
  background-color: #ffffff;
  padding: 20rpx;
  margin-bottom: 20rpx;
}

.module-header {
  margin-bottom: 20rpx;
}

.module-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  position: relative;
  padding-left: 20rpx;
}

.module-title::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 8rpx;
  height: 30rpx;
  background-color: #0863cc;
  border-radius: 4rpx;
}

.recommend-scroll {
  white-space: nowrap;
}

.recommend-list {
  display: flex;
  padding: 10rpx 0;
}

.recommend-item {
  display: inline-block;
  width: 300rpx;
  margin-right: 20rpx;
  background-color: #ffffff;
  border-radius: 12rpx;
  overflow: hidden;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.05);
}

.recommend-image {
  width: 300rpx;
  height: 300rpx;
}

.recommend-info {
  padding: 16rpx;
}

.recommend-title {
  font-size: 26rpx;
  color: #333;
  line-height: 1.4;
  height: 72rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  white-space: normal;
}

.recommend-price-row {
  display: flex;
  align-items: center;
  margin-top: 10rpx;
}

.recommend-price {
  font-size: 30rpx;
  color: #ff5722;
  font-weight: bold;
}

.recommend-original-price {
  font-size: 22rpx;
  color: #999;
  text-decoration: line-through;
  margin-left: 10rpx;
}

.product-list-header {
  background-color: #ffffff;
  padding: 30rpx 20rpx;
  margin-bottom: 2rpx;
}

.product-list-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  position: relative;
  padding-left: 20rpx;
}

.product-list-title::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 8rpx;
  height: 30rpx;
  background-color: #0863cc;
  border-radius: 4rpx;
}

.product-list {
  display: flex;
  flex-wrap: wrap;
  padding: 10rpx;
}

.product-item {
  width: calc(50% - 20rpx);
  margin: 10rpx;
  background-color: #ffffff;
  border-radius: 12rpx;
  overflow: hidden;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.05);
}

.product-image {
  width: 100%;
  height: 345rpx;
}

.product-info {
  padding: 20rpx;
}

.product-title {
  font-size: 28rpx;
  color: #333;
  line-height: 1.4;
  height: 80rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.product-price-row {
  display: flex;
  align-items: center;
  margin-top: 16rpx;
}

.product-price {
  font-size: 32rpx;
  color: #ff5722;
  font-weight: bold;
}

.product-original-price {
  font-size: 24rpx;
  color: #999;
  text-decoration: line-through;
  margin-left: 16rpx;
}

.loading-more {
  text-align: center;
  padding: 30rpx 0;
  color: #999;
  font-size: 24rpx;
}
</style>