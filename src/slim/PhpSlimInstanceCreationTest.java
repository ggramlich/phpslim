// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.*;

import java.lang.reflect.Proxy;

import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;

import fitnesse.slim.SlimServer;
import fitnesse.slim.StatementExecutorInterface;

public class PhpSlimInstanceCreationTest {
  private StatementExecutorInterface caller;

  private static SlimFactory slimFactory;
  
  @BeforeClass
  public static void setUpClass() {
    // Creates Bridge only once
    slimFactory = new PhpSlimFactory();
  }
  
  @Before
  public void setUp() throws Exception {
    caller = slimFactory.getStatementExecutor();
  }

  @Test
  public void canCreateInstance() throws Exception {
    Object response = caller.create("x", "TestModule_TestSlim", new Object[0]);
    assertEquals("OK", response);
    Proxy x = (Proxy) caller.getInstance("x");
    assertNotNull(x);
  }

  @Test
  public void canCreateInstanceWithArguments() throws Exception {
    Object response = caller.create("x", "TestModule_TestSlimWithArguments", new Object[]{"3"});
    assertEquals("OK", response);
    Proxy x = (Proxy) caller.getInstance("x");
    assertNotNull(x);
  }


  @Test
  public void cantCreateInstanceIfConstructorArgumentCountIncorrect() throws Exception {
    String result = (String) caller.create("x", "TestModule_TestSlimWithArguments", new Object[]{"3", "4"});
    assertException("message:<<COULD_NOT_INVOKE_CONSTRUCTOR TestModule_TestSlimWithArguments[2]>>", result);
  }

  @Test
  public void throwsInstanceNotCreatedErrorIfNoSuchClass() throws Exception {
    String result = (String) caller.create("x", "TestModule_NoSuchClass", new Object[0]);
    assertException("message:<<COULD_NOT_INVOKE_CONSTRUCTOR TestModule_NoSuchClass", result);
  }

  @Test
  public void throwsInstanceNotCreatedErrorIfNoPublicDefaultConstructor() throws Exception {
    String result = (String) caller.create("x", "TestModule_ClassWithNoPublicConstructor", new Object[0]);
    assertException("message:<<COULD_NOT_INVOKE_CONSTRUCTOR TestModule_ClassWithNoPublicConstructor[0]>>", result);
  }

  private void assertException(String message, String result) {
    assertTrue(result, result.indexOf(SlimServer.EXCEPTION_TAG) != -1 && result.indexOf(message) != -1);
  }
}
