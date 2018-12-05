---
layout: post
title:  "STM32中GPIO(通用输入输出)使用"
date:   2018-12-4 22:52:00
categories: STM32
tags: STM32 C 嵌入式
excerpt: GPIO
mathjax: true
---

前段时间在做嵌入式工作室招新题时，涉及到一下几个内容：`PWM调光`，`ADC读取电压值`，`通过NPN三极管控制LED灯`。其中`PWM调光`，`ADC读取电压值`较为复杂，我留到后两篇博客中写。

##首先，最简单的是通过`NPN三极管`控制LED灯，首先关于三极管有以下基本知识：

    1.三极管有三个引脚，分别是`基极(B)`，`集电极(C)`和`发射极(E)`。电流由`集电极`和`基极`流入，从`发射极`流出。三极管的放大作用就是：集电极电流受基极电流的控制(假设电源能够提供给集电极足够大的电流的话)，并且基极电流很小的变化，会引起集电极电流很大的变化，且变化满足一定的比例关系：集电极电流的变化量是基极电流变化量的β倍，即电流变化被放大了β倍，所以我们把β叫做三极管的放大倍数(β一般远大于1，例如几十，几百)。如果我们将一个变化的小信号加到基极跟发射极之间，这就会引起`基极电流Ib`的变化，Ib的变化被放大后，导致了Ic很大的变化。如果`集电极电流Ic`是流过一个电阻R的，那么根据电压计算公式U=R*I可以算得，这电阻上电压就会发生很大的变化。我们将这个电阻上的电压取出来，就得到了放大后的电压信号了。而在本题中，需要使用NPN三极管控制LED的点亮与熄灭，那么就通过控制基极上的高低电平，来控制集电极电流IC的大小（有无），来达到控制LED的效果。

	![](https://i.imgur.com/UWe1tYk.jpg)

	`图1：三极管引脚图`

	![](https://i.imgur.com/K6ikYHj.png)

	`控制电路图`

	2.要控制三级管的发射极电流有无，仅需在基极加上很小的电流即可，否则发射极要承受基极几十上百倍的电流，可能导致三极管和单片机烧毁。故在GPIO引脚接入基极之前串联一个10kΩ左右的限流电阻，来使GPIO只需控制高低电平（高电平3.3V，低电平GND）即可控制LED点亮。

	3.具体配置：首先，由于需要控制GPIO引脚的高低电平，所以我们将GPIO设置为 `推挽输出` 模式：

	```c
	void GPIOB_Configuration(void)
	{
		GPIO_InitTypeDef GPIOB_InitStructure;  //声明GPIOB初始化结构体

		RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOB,ENABLE); //使能APB2总线外设GPIOB时钟

		GPIOB_InitStructure.GPIO_Pin = GPIO_Pin_12;  //启用GPIOB12
		GPIOB_InitStructure.GPIO_Mode = GPIO_Mode_Out_PP;  //设置推挽输出模式
		GPIOB_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;  //速度：50MHz
		GPIO_Init(GPIOB,&GPIO_InitStructure);
	}
	```

	这样，我们就可以通过调用`GPIOB_Configuration`函数来初始化我们的GPIO引脚。在通过设置相应寄存器来控制GPIO引脚的电平。那么此处涉及到三个GPIO相关寄存器：`ODR`、`BRR`、`BSRR`。其实GPIO相关的寄存器还有`CRL`、`CRH`这两个寄存器，是用于配置GPIO模式的寄存器，但由于模式和速度对应的二进制码难以记忆，我们直接使用库函数(如以上代码所示)进行初始化即可。回到我们接下来要用到的这三个寄存器，首先，`ODR`寄存器，该寄存器叫作：`端口输出数据寄存器`，也就是直接控制GPIO电平的寄存器，通过16位读写，通常采用4位16进制数或者位操作来读写。设置为1时GPIO输出高电平，设置为0时输出低电平。其次，`BRR`寄存器，全名：`端口位清除寄存器`，顾名思义，控制这个寄存器可以清除ODR寄存器上对应位。设置BRR寄存器为1，即可清除对应ODR寄存器位的值为0，即设置低电平。同理，`BSRR`寄存器，既能控制管脚为高电平，也能控制管脚为低电平。对寄存器高 16bit 写1 对应管脚为低电平，对寄存器低16bit写1对应管脚为高电平。写 0 ,无动作。因此，我们主程序中可以这么写：

	```c
	#include "stm32f10x.h"

	int main(void)
	{
		SystemInit();
		GPIOB_Configuration();  //配置GPIOB

		GPIOB->BSRR |= 0x1000;
		while(1);
	}
	```
	这样，我们就通过设置BSRR寄存器的值完成了将GPIOB12管脚设置为高电平。这步操作也可直接使用库函数来代替：

	```c
	#include "stm32f10x.h"

	int main(void)
	{
		SystemInit();
		GPIOB_Configuration();  //配置GPIOB

		GPIO_SetBits(GPIOB,GPIO_Pin_12);
		while(1);
	}
	```

	如果我们要设置低电平，设置BRR寄存器对应位为1，或使用库函数 `GPIO_ResetBits()`即可。至此，使用GPIO+NPN三极管控制LED灯就全部结束了。


##疑问：

###刚开始或许你跟我一样有以下疑惑：

####1.既然ODR 能控制管脚高低电平为什么还需要BSRR和SRR寄存器？
####2.既然BSRR能实现BRR的全部功能，为什么还需要SRR寄存器？

对于问题 1 ------ 意法半导体给的答案是---

“This way, there is no risk that an IRQ occurs between the read and the modify access.”
什么意思呢？就就是你用BSRR和BRR去改变管脚状态的时候，没有被中断打断的风险。也就不需要关闭中断。

用ODR操作GPIO的伪代码如下：

disable_irq()
save_gpio_pin_sate = read_gpio_pin_state();
save_gpio_pin_sate = xxxx;
chang_gpio_pin_state(save_gpio_pin_sate);
enable_irq();

关闭中断明显会延迟或丢失一事件的捕获，所以控制GPIO的状态最好还是用SBRR和BRR。



对于问题 2 ------- 个人经验判断意法半导体仅仅是为了程序员操作方便估计做么做的。

因为BSRR的 低 16bsts 恰好是set操作，而高16bit是 reset 操作 而BRR 低 16bits 是reset 操作。

简单地说GPIOx_BSRR的高16位称作清除寄存器，而GPIOx_BSRR的低16位称作设置寄存器。

另一个寄存器GPIOx_BRR只有低16位有效，与GPIOx_BSRR的高16位具有相同功能。

举个例子说明如何使用这两个寄存器和所体现的优势。

例如GPIOE的16个IO都被设置成输出，而每次操作仅需要

改变低8位的数据而保持高8位不变，假设新的8位数据在变量Newdata中，

这个要求可以通过操作这两个寄存器实现，STM32的固件库中有两个函数

GPIO_SetBits()和GPIO_ResetBits()使用了这两个寄存器操作端口。

上述要求可以这样实现：

GPIO_SetBits(GPIOE, Newdata & 0xff);
GPIO_ResetBits(GPIOE, (~Newdata & 0xff));

也可以直接操作这两个寄存器：

GPIOE->BSRR = Newdata & 0xff;
GPIOE->BRR = ~Newdata & 0xff;

当然还可以一次完成对8位的操作：

GPIOE->BSRR = (Newdata & 0xff) | ( (~Newdata & 0xff)<<16 );

当然还可以一次完成对16位的操作：

GPIOE->BSRR = (Newdata & 0xffff) | ( (~Newdata )<<16 );

从最后这个操作可以看出使用BSRR寄存器，可以实现8个端口位的同时修改操作。

有人问是否BSRR的高16位是多余的，请看下面这个例子：

假如你想在一个操作中对GPIOE的位7置'1'，位6置'0'，则使用BSRR非常方便： 
  GPIOE->BSRR = 0x400080; 

如果没有BSRR的高16位，则要分2次操作，结果造成位7和位6的变化不同步！ 
  GPIOE->BSRR = 0x80; 
  GPIOE->BRR = 0x40;



=========================================
以上对于ODR寄存器，BRR寄存器，BSRR寄存器三者关系的解释，参考自：http://www.cnblogs.com/shangdawei/p/4723941.html