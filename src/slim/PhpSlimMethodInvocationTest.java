// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.*;
import static slim.TestSuite.getTestIncludePath;

import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;

import slim.testModule.TestSlimProxy;
import fitnesse.slim.SlimFactory;
import fitnesse.slim.SlimMethodInvocationTestBase;


public class PhpSlimMethodInvocationTest extends SlimMethodInvocationTestBase {
  private static SlimFactory slimFactory;

  @Override
  protected String getTestClassName() {
    return "TestModule_TestSlim";
  }
  
  @BeforeClass
  public static void setUpClass() {
    // Creates Bridge only once
    slimFactory = new PhpSlimFactory(getTestIncludePath());
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }

  @Before
  @Override
  public void setUp() throws Exception {
    caller = slimFactory.getStatementExecutor();
    caller.create("testSlim", getTestClassName(), new Object[0]);
    testSlim = new TestSlimProxy("testSlim", caller);
  }

  @Override
  @Test
  public void methodReturnsVoid() throws Exception {
    // There is no difference in PHP between functions returning null and void.
    Object retval = caller.call("testSlim", "nilad");
    assertNull(retval);
  }
}
