<template>
  <view class="subcategory-container">
    <!-- 三级分类筛选 -->
    <view class="filter-section">
      <scroll-view class="filter-scroll" scroll-x="true" show-scrollbar="false">
        <view class="filter-list">
          <view 
            class="filter-item" 
            :class="{ active: currentFilter === 0 }"
            @click="switchFilter(0)"
          >
            <text>全部</text>
          </view>
          <view 
            v-for="(item, index) in thirdCategories" 
            :key="index" 
            class="filter-item"
            :class="{ active: currentFilter === item.id }"
            @click="switchFilter(item.id)"
          >
            <text>{{ item.name }}</text>
          </view>
        </view>
      </scroll-view>
    </view>
    
    <!-- 商品列表 -->
    <view class="product-list">
      <view 
        v-for="(item, index) in products" 
        :key="index" 
        class="product-item"
        @click="navigateToDetail(item)"
      >
        <image class="product-image" :src="item.slides && item.slides.length > 0 ? item.slides[0] : (item.image || '/static/images/default-product.png')" mode="aspectFill"></image>
        <view class="product-info">
          <text class="product-title">{{ item.title }}</text>
          <view class="product-price-row">
            <text class="product-price">¥{{ item.price }}</text>
            <text class="product-original-price">¥{{ item.original_price }}</text>
          </view>
          <view class="product-extra-info">
            <view class="product-tag" v-if="item.is_new">新品</view>
            <view class="product-tag" v-if="item.is_hot">热销</view>
            <text class="product-sales">已售{{ item.sales || 0 }}件</text>
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
import Api from '@/utils/api.js';

export default {
  components: {
    ZhuigeNodata
  },
  
  data() {
    return {
      subCategoryId: 0,
      thirdCategories: [],
      currentFilter: 0,
      products: [],
      page: 1,
      isLoading: false,
      hasMore: true,
      apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige-xcx/v1'
    }
  },
  
  onLoad(options) {
    console.log('二级分类详情页面加载');
    
    if (options.id) {
      this.subCategoryId = parseInt(options.id);
      console.log('二级分类ID:', this.subCategoryId);
      this.loadThirdCategories();
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
    // 加载三级分类
    loadThirdCategories() {
      console.log('开始加载三级分类数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/thirdcategories';
      console.log('请求URL:', apiUrl);
      console.log('请求参数:', { parent_id: this.subCategoryId });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          parent_id: this.subCategoryId
        },
        success: (res) => {
          console.log('三级分类数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            // 三级分类已经按照 sort_order 排序，直接使用
            this.thirdCategories = res.data.data;
            console.log('获取到三级分类数据:', this.thirdCategories);
          } else {
            console.error('三级分类数据请求返回错误:', res.data);
          }
        },
        fail: (err) => {
          console.error('三级分类数据请求失败:', err);
          uni.showToast({
            title: '加载三级分类失败',
            icon: 'none'
          });
        }
      });
    },
    
    // 加载商品
    loadProducts() {
      this.isLoading = true;
      this.page = 1;
      this.products = [];
      
      console.log('开始加载商品数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/products';
      console.log('请求URL:', apiUrl);
      console.log('请求参数:', {
        category_id: this.currentFilter || this.subCategoryId,
        page: this.page,
        per_page: 10
      });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          category_id: this.currentFilter || this.subCategoryId,
          page: this.page,
          per_page: 10
        },
        success: (res) => {
          console.log('商品数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = res.data.data.list;
            this.hasMore = res.data.data.more;
            console.log('获取到商品数据:', this.products);
          } else {
            console.error('商品数据请求返回错误:', res.data);
            uni.showToast({
              title: res.data.msg || '加载商品失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          console.error('商品数据请求失败:', err);
          uni.showToast({
            title: '网络错误',
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
          uni.stopPullDownRefresh();
          console.log('商品数据请求完成');
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
      
      console.log('加载更多商品数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/products';
      console.log('请求参数:', {
        category_id: this.currentFilter || this.subCategoryId,
        page: this.page,
        per_page: 10
      });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          category_id: this.currentFilter || this.subCategoryId,
          page: this.page,
          per_page: 10
        },
        success: (res) => {
          console.log('加载更多商品数据成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = [...this.products, ...res.data.data.list];
            this.hasMore = res.data.data.more;
          } else {
            this.page--;
            console.error('加载更多商品数据返回错误:', res.data);
            uni.showToast({
              title: res.data.msg || '加载更多失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          this.page--;
          console.error('加载更多商品数据失败:', err);
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
    
    // 切换筛选
    switchFilter(filterId) {
      if (this.currentFilter === filterId) {
        return;
      }
      
      console.log('切换筛选:', filterId);
      this.currentFilter = filterId;
      this.loadProducts();
    },
    
    // 刷新数据
    refreshData() {
      this.loadThirdCategories();
      this.loadProducts();
    },
    
    // 跳转到商品详情
    navigateToDetail(product) {
      if (product.app_id && product.path) {
        // 跳转到其他小程序
        console.log('跳转到小程序:', product.app_id, product.path);
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
      } else {
        console.warn('商品未配置跳转信息:', product);
        uni.showToast({
          title: '该商品未配置跳转信息',
          icon: 'none'
        });
      }
    }
  }
}
</script>

<style scoped>
.subcategory-container {
  min-height: 100vh;
  background-color: #f8f8f8;
  padding-bottom: 30rpx;
}

.filter-section {
  background-color: #ffffff;
  position: sticky;
  top: 0;
  z-index: 10;
  box-shadow: 0 2rpx 6rpx rgba(0, 0, 0, 0.05);
}

.filter-scroll {
  height: 90rpx;
  white-space: nowrap;
}

.filter-list {
  display: flex;
  padding: 0 20rpx;
}

.filter-item {
  display: inline-block;
  padding: 0 30rpx;
  height: 90rpx;
  line-height: 90rpx;
  font-size: 28rpx;
  color: #333;
  position: relative;
}

.filter-item.active {
  color: #ff5722;
  font-weight: bold;
}

.filter-item.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60rpx;
  height: 6rpx;
  background-color: #ff5722;
  border-radius: 3rpx;
}

.product-list {
  display: flex;
  flex-wrap: wrap;
  padding: 20rpx;
  margin-top: 20rpx;
}

.product-item {
  width: calc(50% - 20rpx);
  margin: 10rpx;
  background-color: #ffffff;
  border-radius: 12rpx;
  overflow: hidden;
  box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
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

.product-extra-info {
  display: flex;
  align-items: center;
  margin-top: 12rpx;
}

.product-tag {
  font-size: 20rpx;
  color: #ffffff;
  background-color: #ff5722;
  padding: 2rpx 8rpx;
  border-radius: 4rpx;
  margin-right: 10rpx;
}

.product-sales {
  font-size: 22rpx;
  color: #999;
}

.loading-more {
  text-align: center;
  padding: 30rpx 0;
  color: #999;
  font-size: 24rpx;
}
</style> 